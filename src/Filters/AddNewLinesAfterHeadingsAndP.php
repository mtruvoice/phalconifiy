<?php

namespace Phalconify\Filters;

/**
 * Class RemoveInlineStyles
 * @package Phalconify\Filters
 *
 * Phalcon filter to add new lines to headings and p tags
 */
class AddNewLinesAfterHeadingsAndP
{
    /**
     * @param $value
     * @return string
     */
    public function filter($value)
    {
        return str_replace(["</h1>", "</h2>", "</h3>", "</h4>", "</h5>", "</h6>", "</p>"],
            ["</h1>\n\n", "</h2>\n\n", "</h3>\n\n", "</h4>\n\n", "</h5>\n\n", "</h6>\n\n", "</p>\n\n"], $value);
    }
}