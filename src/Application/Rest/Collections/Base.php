<?php

namespace Phalconify\Application\Rest\Collections;

/**
 * Implements an abstraction for collections.
 */
abstract class Base extends \Phalcon\Mvc\MongoCollection implements \JsonSerializable
{
    use Loader;
    use Pagination;
    use Sorting;

    public $dateUpdated;

    public $dateCreated;

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $this->_id = (string)$this->_id;

        return $this->getData();
    }

    /**
     * Gets the conditions to apply.
     *
     * @param array $query
     *                     Query parameters
     *
     * @return array
     */
    public static function getConditions($query = [])
    {
        $properties = self::getProperties();

        return array_intersect_key($query, array_flip($properties));
    }

    /**
     * Fetch all fields based on pagination filter and sorting filter, key limited.
     *
     * @return array
     */
    public static function fetch($query = [])
    {
        $filter = self::getPaginationFilter($query);
        $filter += self::getSortingFilter($query);
        $filter += [
            'fields' => static::getProperties(),
            'conditions' => static::getConditions($query),
        ];

        return static::find($filter);
    }

    /**
     * Gets total number of entries matched by the query.
     *
     * @param array $query
     *                     Query
     *
     * @return int
     */
    public static function total($query = [])
    {
        return static::count(['conditions' => static::getConditions($query)]);
    }

    /**
     * Return a string interpretation of the of error messages.
     *
     * @return string
     */
    public function getMessagesAsString()
    {
        $messages = $this->getMessages();

        return implode(', ', $messages);
    }

    /**
     * Return messages without associative keys.
     *
     * @return array
     */
    public function getMessagesAsArray()
    {
        return array_map(function (\Phalcon\Mvc\Model\Message $message) {
            return $message->getMessage();
        }, $this->getMessages());
    }

    public function getDateUpdated()
    {
        return $this->dateUpdated();
    }

    public function setDateUpdated($time)
    {
        $this->dateUpdated = $time;

        return $this;
    }

    public function getDateCreated()
    {
        return $this->dateUpdated();
    }

    public function setDateCreated($time)
    {
        $this->dateCreated = $time;

        return $this;
    }

    /**
     * Model Hook: After validation on create.
     */
    public function afterValidationOnCreate()
    {
        $time = time();
        $this->setDateCreated($time);
        $this->setDateUpdated($time);
    }

    /**
     * Model Hook: After validation.
     */
    public function afterValidation()
    {
        $time = time();
        $this->setDateUpdated($time);
    }

    /**
     * Extend a parent \Phalcon\Mvc\Collection::aggregate() method.
     * Added possible to generate $limit and $skip params based on GET request or default values.
     *
     * @param array $params
     *                     Query parameters
     *
     * @return array
     */
    public static function aggregate(array $params = null)
    {
        $request = new \Phalconify\Application\Rest\Http\Request;
        $page = $request->get('page', null, 1);
        $limit = $request->get('limit', null, self::getDefaultLimit());
        $skip = 0;

        foreach ($params as $k => $item){
            if (key($item) == '$limit' || key($item) == '$skip'){
                if (key($item) == '$limit'){
                    $limit = $item['$limit'];
                }
                elseif (key($item) == '$skip'){
                    $skip = $item['$skip'];
                }

                unset($params[$k]);
            }
        }

        if ($skip !== 0 || $page > 1){
            $skip = ($skip !== 0) ? $skip : ($page-1)*$limit;
        }

        $params[] = ['$limit' => $skip+$limit];
        $params[] = ['$skip' => $skip];

        return parent::aggregate($params);
    }
}
