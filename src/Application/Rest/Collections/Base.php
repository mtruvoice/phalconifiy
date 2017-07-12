<?php

namespace Phalconify\Application\Rest\Collections;

use Phalcon\Db\Adapter\MongoDB\Model\BSONDocument;

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
     * @param array $params Query parameters
     * @param bool $pagination Pagination logic required for request.
     *
     * @return bool|BSONDocument|array false if failed / array if results with pagination. BSONDocument if no pagination.
     *
     */
    public static function aggregate(array $params = null, bool $pagination = false)
    {
        if ($pagination) {
            return self::glueAggregatePagination($params);

        } else {
            return parent::aggregate($params);
        }
    }

    /**
     * Adds pagination logic to aggregate pipeline
     * @param array $params array of aggregate pipeline
     *
     * @return bool|BSONDocument
     */
    protected static function glueAggregatePagination($params)
    {
        // sort out default get parameters
        $request = new \Phalconify\Application\Rest\Http\Request;
        $page = $request->get('page', null, 1);
        $limit = $request->get('limit', null, self::getDefaultLimit());
        $skip = 0;
        if ($skip !== 0 || $page > 1) {
            $skip = ($skip !== 0) ? $skip : ($page - 1) * $limit;
        }

        // add pagination logic to pipeline
        $params[] = [
            '$group' =>
                [
                    '_id' => 'null',
                    'many' => ['$sum' => 1],
                    'all' => ['$push' => '$$ROOT']
                ]];
        $params[] = [
            '$project' =>
                [
                    '_id' => 0,
                    'totalRecords' => '$many',
                    'records' => ['$slice' => ['$all', (int)$skip, (int)$limit]],
                ]
        ];

        // make request and save as array
        $result = parent::aggregate($params)->toArray();

        // add pageLimit to document
        if (true or isset($result[0]) && isset($result[0]['records']) && count($result[0]['records']) > 0) {
            $result[0]['pageLimit'] = $limit;

            return $result[0];
        } else { // return false if nothing found
            return false;
        }
    }
}
