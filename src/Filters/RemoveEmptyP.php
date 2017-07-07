<?php

namespace Phalconify\Filters;

/**
 * Class RemoveEmptyP
 * @package Phalconify\Filters
 *
 * Phalcon filter to remove empty p tags
 */
class RemoveEmptyP
{
    /**
     * @param $value
     * @return string
     */
    public function filter($value)
    {
        $value = preg_replace([
            '#<p>\s*<(div|aside|section|article|header|footer)#',
            '#</(div|aside|section|article|header|footer)>\s*</p>#',
            '#</(div|aside|section|article|header|footer)>\s*<br ?/?>#',
            '#<(div|aside|section|article|header|footer)(.*?)>\s*</p>#',
            '#<p>\s*</(div|aside|section|article|header|footer)#',
        ], ['<$1', '</$1>', '</$1>', '<$1$2>', '</$1',], $value);

        return preg_replace('#<p>(\s|&nbsp;)*+(<br\s*/*>)*(\s|&nbsp;)*</p>#i', '', $value);
    }
}