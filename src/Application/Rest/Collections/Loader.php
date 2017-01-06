<?php

namespace Phalconify\Application\Rest\Collections;

use Phalcon\FilterInterface;
use Phalconify\Application\Rest\Http\Request;

/**
 * Enables a collection to be loaded via different inputs.
 *
 * @author armonb
 */
trait Loader
{
    /**
     * Gets the property values of the collection.
     *
     * @return array
     */
    protected function getData()
    {
        return array_intersect_key(get_object_vars($this), array_flip(static::getProperties()));
    }

    /**
     * Gets the properties of the collection to use in the collection.
     *
     * @return array
     */
    protected static function getProperties()
    {
        $reflection = new \ReflectionClass(get_called_class());
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        return array_map(function (\ReflectionProperty $item) {
            return $item->getName();
        }, $properties);
    }

    /**
     * Loads data into the collection from the request POST.
     *
     * @param Request $request
     *                         Request
     * @param array   $ignore
     *                         List of parameters to ignore
     */
    public function loadFromPOST(Request $request, $ignore = [], FilterInterface $filter = null)
    {
        $data = $request->getPost();
        $this->loadFromData($data, $ignore, $filter);
    }

    /**
     * Loads data into the collection from the given request.
     *
     * @param Request $request
     *                         Request
     * @param array   $ignore
     *                         List of parameters to ignore
     */
    public function loadFromBody(Request $request, $ignore = [], FilterInterface $filter = null)
    {
        $data = $request->getJsonBody();
        $this->loadFromData($data, $ignore, $filter);
    }

    /**
     * Loads data into the collection from the given request.
     *
     * @param array $data
     *                      Data to set in the object
     * @param array $ignore
     *                      List of parameters to ignore
     */
    public function loadFromData(array $data, $ignore = [], FilterInterface $filter = null)
    {
        $ignore = $ignore + ['__pclass', '_id'];
        $properties = $this->getProperties();
        $data = $this->sanitize($data, $filter);
        foreach ($data as $key => $value) {
            if (in_array($key, $properties) && !in_array($key, $ignore)) {
                // Trim value
                $value = (!is_array($value) && !is_object($value)) ? trim($value) : $value;

                // Look for a setter
                $setter = 'set'.ucfirst($key);
                if (method_exists($this, $setter)) {
                    $this->$setter($value);
                } else {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * Default sanitisation of the data.
     *
     * @param array                                    $data
     *                                                         Data to sanitise
     * @param \ArmonB\Rest\Collections\FilterInterface $filter
     *                                                         Filter to use
     *
     * @return array
     *               Data sanitised
     */
    protected function sanitize($data, FilterInterface $filter = null)
    {
        if ($filter) {
            array_walk_recursive($data, function (&$value) use ($filter) {
                $data = $filter->sanitize($value, ['trim', 'string']);
            });
        }

        return $data;
    }
}
