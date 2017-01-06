<?php

namespace Phalconify\Application\Rest\Http;

/**
 * Implements behaviour related with the user agent of the request.
 */
trait UserAgent
{
    /**
     * Attempts to identify the users browser by the useragent string.
     *
     * @return string The browser name detected
     */
    public function getUserBrowser()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
            return 'Internet Explorer';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) {
            return 'Internet Explorer';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
            return 'iPad';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) {
            return 'iPhone';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false) {
            return 'Android';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) {
            return 'Mozilla Firefox';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false) {
            return 'Google Chrome';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false) {
            return 'Opera Mini';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false) {
            return 'Opera';
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false) {
            return 'Safari';
        } else {
            return 'Unknown';
        }
    }

    /**
     * Attempts to identify if the useragent is a bot.
     *
     * @return bool
     */
    public static function isBot()
    {
        return (bool) preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT']);
    }
}
