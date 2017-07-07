<?php

namespace Phalconify\Filters;

/**
 * Class UTF8
 * @package Phalconify\Filters
 *
 * Phalcon filter to process a string to a utf-8 string
 */
class UTF8
{
    /**
     * @param $value
     * @return string
     */
    public function filter($value)
    {
        $value = iconv(mb_detect_encoding($value, mb_detect_order(), true), 'UTF-8', $value);

        return (string)iconv('UTF-8', 'UTF-8//IGNORE', $value);
    }
}