<?php

namespace Phalconify\Application\Rest\Collections;

/**
 * Concrete implementation of the pagination functionality.
 */
trait Pagination
{
    /**
     * Default values.
     */
    protected static $defaultPage = 1;
    protected static $defaultLimit = 20;

    /**
     * Gets default page.
     *
     * @return int
     */
    protected static function getDefaultPage()
    {
        return static::$defaultPage;
    }

    /**
     * Gets default limit.
     *
     * @return int
     */
    protected static function getDefaultLimit()
    {
        return static::$defaultLimit;
    }

    /**
     * Gets the page option to use.
     *
     * @param array $query
     *                     Query parameters
     *
     * @return int
     */
    protected static function getPage($query)
    {
        return isset($query['page']) && is_numeric($query['page']) ? $query['page'] : static::getDefaultPage();
    }

    /**
     * Gets the limit entries to use.
     *
     * @param array $query
     *                     Query parameters
     *
     * @return int
     */
    public static function getLimit($query)
    {
        return isset($query['limit']) && is_numeric($query['limit']) ? $query['limit'] : static::getDefaultLimit();
    }

    /**
     * Gets the skip parameter to use in queries.
     *
     * @param array $query
     *                     Query parameters
     *
     * @return int
     */
    protected static function getSkip($query)
    {
        return (static::getLimit($query) * static::getPage($query)) - static::getLimit($query);
    }

    /**
     * Retrieves the pagination filter, removing the pagination parameters from the query.
     *
     * @param array $query
     *                     Query parameters
     *
     * @return array
     *               Filter to use in the collection
     */
    protected static function getPaginationFilter(&$query)
    {
        $filter = [
            'limit' => self::getLimit($query),
            'skip' => self::getSkip($query),
        ];
        unset($query['limit'], $query['page']);

        return $filter;
    }
}
