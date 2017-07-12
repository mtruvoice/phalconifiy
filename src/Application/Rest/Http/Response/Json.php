<?php

namespace Phalconify\Application\Rest\Http\Response;

/**
 * Implements a Json response handler.
 */
class Json extends General implements ResponseInterface
{
    /**
     * Sets an error JSON response.
     *
     * @param array $messages Messages to show to the user
     * @param mixed $payload Additional payload to return, such objects, etc
     * @param int $code Return status code
     *
     * @return ResponseInterface
     */
    public static function error(array $messages = ['error'], $payload = null, int $code = self::CODE_BAD_REQUEST)
    {
        $response = new static();
        $response->setStatusCode($code, self::CODE_BAD_REQUEST);

        return $response->setJsonContent([
            'status' => self::ERROR,
            'messages' => $messages,
            'payload' => $payload,
        ]);
    }

    /**
     * Returns  Denied JSON error response. Denied = not enough privileges-permissions
     *                           - server understood the request but denies the access
     * @param array $messages Message to show to user
     * @param null $payload Additional payload to return
     * @param int $code Return status code
     * @return ResponseInterface
     */
    public static function denied(array $messages = ['access denied'], $payload = null, int $code = self::CODE_DENIED)
    {
        $response = new static();
        $response->setStatusCode($code, self::ERROR_PERMISSION_DENIED);

        return $response->setJsonContent([
            'status' => self::ERROR_PERMISSION_DENIED,
            'messages' => $messages,
            'payload' => $payload
        ]);
    }

    /**
     * Returns Unauthorized JSON error response. Unauthorized = not logged in.
     *
     * @param array $messages Message to show to user
     * @param null $payload Additional payload to return
     * @param int $code Return status code
     * @return ResponseInterface
     */
    public static function unauthorized(array $messages = ['access unauthorized'], $payload = null, int $code = self::CODE_UNAUTHORIZED)
    {
        $response = new static();
        $response->setStatusCode($code, self::ERROR_UNAUTHORIZED);

        return $response->setJsonContent([
            'status' => self::ERROR_UNAUTHORIZED,
            'messages' => $messages,
            'payload' => $payload
        ]);
    }

    /**
     * Sets a success JSON response.
     *
     * @param array $messages Messages to show to the user
     * @param mixed $payload Additional payload to return, such objects, etc
     * @param int $code Return status code
     * @return ResponseInterface
     */
    public static function success(array $messages = ['success'], $payload = null, int $code = self::CODE_OK)
    {
        $response = new static();
        $response->setStatusCode($code);

        return $response->setJsonContent([
            'status' => self::SUCCESS,
            'messages' => $messages,
            'payload' => $payload,
        ]);
    }

    /**
     * Sets a not found response.
     *
     * @param array $messages Messages to show to the user
     * @return ResponseInterface
     */
    public static function notFound(array $messages = ['not found'])
    {
        $response = new static();
        $response->setStatusCode(self::CODE_NOT_FOUND);

        return $response->setJsonContent([
            'status' => self::ERROR,
            'messages' => $messages,
        ]);
    }

    /**
     * Appends Json content to the object.
     *
     * @param array $content
     */
    public function appendJsonContent($content)
    {
        $current = $this->getContent();
        $json = json_decode($current);
        $json = $json + $content;
        $this->setJsonContent($json);
    }

    /**
     * Appends data to the payload.
     *
     * @param array $content
     */
    public function appendToPayload(array $content)
    {
        $current = $this->getContent();
        $json = (array)json_decode($current, true);
        $json['payload'] += (array)$content;
        $this->setJsonContent($json);
    }
}
