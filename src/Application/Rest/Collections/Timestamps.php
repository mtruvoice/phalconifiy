<?php

namespace Phalconify\Application\Rest\Collections;

/**
 * Enables a record timestamps.
 */
trait Timestamps
{
    /**
     * Key to use for the date updated.
     *
     * @var int
     */
    public static $dateUpdated = null;

    /**
     * Key to use for the date created.
     *
     * @var int
     */
    public static $dateCreated = null;

    /**
     * Gets the date updated.
     *
     * @return int
     */
    protected static function getDateUpdated()
    {
        return static::$dateUpdated;
    }

    /**
     * Sets the date updated.
     *
     * @return int
     */
    protected static function setDateUpdated($timestamp)
    {
        static::$dateUpdated = $timestamp;
    }

    /**
     * Gets the date created.
     *
     * @return int
     */
    protected static function getDateCreated()
    {
        return static::$dateCreated;
    }

    /**
     * Sets the date created.
     *
     * @return int
     */
    protected static function setDateCreated($timestamp)
    {
        static::$dateCreated = $timestamp;
    }
}
