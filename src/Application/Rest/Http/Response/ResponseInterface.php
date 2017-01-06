<?php

namespace Phalconify\Application\Rest\Http\Response;

interface ResponseInterface
{
    public static function error(array $messages, $payload = null);
    public static function success(array $messages, $payload = null);
    public static function notFound(array $messages);
}
