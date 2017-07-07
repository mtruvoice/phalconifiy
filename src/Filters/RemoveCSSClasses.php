<?php

namespace Phalconify\Filters;

/**
 * Class RemoveInlineStyles
 * @package Phalconify\Filters
 *
 * Phalcon filter to remove CSS classes
 */
class RemoveCSSClasses
{
    /**
     * @param $value
     * @return string
     */
    public function filter($value)
    {
        return preg_replace('/class=".*?"/', '', $value);
    }
}