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
     * @param array $messages
     *                        Messages to show to the user
     * @param mixed $payload
     *                        Additional payload to return, such objects, etc
     * @param int $code
     *                       Status Code to return
     *
     * @return ResponseInterface
     */
    public static function error(array $messages, $payload = null, int $code = 400)
    {
        $response = new static(null, $code);
        $response->setStatus(self::ERROR);

        return $response->setJsonContent([
            'status' => self::ERROR,
            'messages' => $messages,
            'payload' => $payload,
        ]);
    }

    /**
     * Sets a success JSON response.
     *
     * @param array $messages
     *                        Messages to show to the user
     * @param mixed $payload
     *                        Additional payload to return, such objects, etc
     *
     * @return ResponseInterface
     */
    public static function success(array $messages, $payload = null)
    {
        $response = new static();
        $response->setStatus(self::SUCCESS);

        return $response->setJsonContent([
            'status' => self::SUCCESS,
            'messages' => $messages,
            'payload' => $payload,
        ]);
    }

    /**
     * Sets a not found response.
     *
     * @param array $messages
     *                        Messages to show to the user
     *
     * @return ResponseInterface
     */
    public static function notFound(array $messages)
    {
        $response = new static(null, 404, 'Not Found');
        $response->setStatus(self::ERROR);

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
        $json = (array) json_decode($current, true);
        $json['payload'] += (array) $content;
        $this->setJsonContent($json);
    }
}
