<?php

namespace Phalconify\Filters;

/**
 * Class RemoveInlineStyles
 * @package Phalconify\Filters
 *
 * Phalcon filter to remove new lines
 */
class RemoveNewLines
{
    /**
     * @param $value
     * @return string
     */
    public function filter($value)
    {
        return trim(preg_replace('/\s\s+/', ' ', $value));
    }
}