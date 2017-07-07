<?php

namespace Phalconify\Filters;

/**
 * Class NL2P
 * @package Phalconify\Filters
 *
 * Phalcon filter to convert new lines to p tags
 */
class NL2P
{
    /**
     * @param $value
     * @return string
     */
    public function filter($value)
    {
        $value = str_replace(['<p>', '</p>', '<br>', '<br />'], '', $value);

        return '<p>' . preg_replace(
                ["/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"],
                ["</p>\n<p>", "</p>\n<p>", '$1<br' . ' /' . '>$2'],
                trim($value)) . '</p>';
    }
}