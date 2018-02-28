<?php

namespace Phalconify\Application\Rest\Collections;

/**
 * Concrete implementation of the sorting functionality.
 */
trait Sorting
{
    /**
     * Default values.
     */
    protected static $defaultSortBy = 'name';
    protected static $defaultSortOrder = 1;

    /**
     * Gets the default sort by option.
     *
     * @return string
     */
    protected static function getDefaultSortBy()
    {
        return static::$defaultSortBy;
    }

    /**
     * Gets the default sort order option.
     *
     * @return string
     */
    protected static function getDefaultSortOrder()
    {
        return static::$defaultSortOrder;
    }

    /**
     * Gets the sort by option to use.
     *
     * @param array $query
     *                     Query parameters
     *
     * @return string
     */
    protected static function getSortBy($query)
    {
        return $query['sortBy'] ?? static::getDefaultSortBy();
    }

    /**
     * Gets the sort order option to use.
     *
     * @param array $query
     *                     Query parameters
     *
     * @return string
     */
    protected static function getSortOrder($query)
    {
        return $query['sortOrder'] ?? static::getDefaultSortOrder();
    }

    /**
     * Retrieves the sorting filter, removing the sorting parameters from the query.
     *
     * @param array $query
     *                     Query parameters
     *
     * @return array
     *               Filter to use in the collection
     */
    protected static function getSortingFilter(&$query)
    {
        $filter = [
            'sort' => [static::getSortBy($query) => (int)static::getSortOrder($query)],
        ];
        unset($query['sortBy'], $query['sortOrder']);

        return $filter;
    }
}
