<?php

namespace Phalconify\Filters;

/**
 * Class RemoveInlineStyles
 * @package Phalconify\Filters
 *
 * Phalcon filter to remove inline styles
 */
class RemoveInlineStyles
{
    /**
     * @param $value
     * @return string
     */
    public function filter($value)
    {
        return preg_replace('#(<[a-z0-9 ]*)(style=("|\')(.*?)("|\'))([a-z0-9 ]*>)#', '\\1\\6', $value);
    }
}