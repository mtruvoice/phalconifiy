<?php

namespace Phalconify\Utils;

/**
 * Implements utilities related with strings.
 */
trait Strings
{
    /**
     * Creates a url friendly slug from a string.
     *
     * @param string $string
     *                       Any given string to be made into a slug
     *
     * @return string
     *                The slug form of the given $string
     */
    public function createSlug($string)
    {
        if (!empty($string)) {
            // Replace non letter or digits by -
            $string = preg_replace('~[^\pL\dz\/]+~u', '-', $string);

            // Transliterate
            $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

            // Remove unwanted characters
            $string = preg_replace('~[^-\w\/]+~', '', $string);

            // Trim
            $string = trim($string, '-');

            // Remove duplicate -
            $string = preg_replace('~-+~', '-', $string);

            // Lowercase
            $string = strtolower($string);
        }

        return $string;
    }

    /**
     * Removes new lines from a string in place of <p></p> or <br> tags.
     *
     * @param string $string
     *                           The string to parse
     * @param bool   $lineBreaks
     *                           Whether to use <br> in place of <p></p>
     *
     * @return string
     *                The string with new tags in place of new lines
     */
    public static function nl2p($string, $lineBreaks = true)
    {
        $string = str_replace(['<p>', '</p>', '<br>', '<br />'], '', $string);

        // It is conceivable that people might still want single line-breaks
        if ($lineBreaks == true) {
            return '<p>'.preg_replace(["/([\n]{2,})/i", "/([^>])\n([^<])/i"], ["</p>\n<p>", '$1<br/>$2'], trim($string)).'</p>';
        } else {
            return '<p>'.preg_replace(["/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"], ["</p>\n<p>", "</p>\n<p>", '$1<br/>$2'], trim($string)).'</p>';
        }

        return $string;
    }

    /**
     * Creates a random string of alphanumeric charaters to given length.
     *
     * @param int $length
     *                    The character length of string required
     *
     * @return string
     *                The random string that was created
     */
    public static function createRandomString($length = 10)
    {
        return \Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM, $length);
    }

    /**
     * Checks if a given string is a json string.
     *
     * @param string $string
     *                       String to check
     *
     * @return bool
     *              Whether or not the string is json
     */
    public static function isJson($string)
    {
        $decode = json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * Checks if a given string is valid xml.
     *
     * @param string $xml
     *                    String to check
     *
     * @return bool
     *              Whether or not the string is xml
     */
    public static function isXml($xml)
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);
        $errors = libxml_get_errors();

        return empty($errors);
    }
}
