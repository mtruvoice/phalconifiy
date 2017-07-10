<?php

namespace Phalconify\Application\Rest\Http;

/**
 * Implements a wrapper on the Phalcon request handler.
 */
class Request extends \Phalcon\Http\Request
{
    use UserAgent;

    /**
     * Gets the query data filtered.
     *
     * @return array
     */
    public function getQueryData()
    {
        $query = $this->get() ?: [];

        return $this->filterQueryData($query);
    }

    /**
     * Filters the query data.
     *
     * Here we can remove those keys that we don't want to be processed.
     *
     * @param array $query Query parameters
     *
     * @return array
     */
    protected function filterQueryData($query = [])
    {
        return array_diff_key($query, array_flip([
            '_url',
            'XDEBUG_SESSION_START',
        ]));
    }

    /**
     * Gets a JSON from the body request.
     *
     * @return type
     */
    public function getJsonBody()
    {
        $json = $this->getRawBody();

        return json_decode($json, true);
    }

    /**
     * Gets the authentication token.
     *
     * @return string
     */
    public function getAuthToken()
    {
        // Get request headers
        $headers = $this->getHeaders();
        if (isset($headers['Authorization'])) {
            $bearer = $headers['Authorization'];

            $token = trim(str_replace('Bearer', '', $bearer));
        } else {
            // Is token present as get request param
            $token = $_GET['auth_token'] ?? false;
        }

        return $token;
    }
}
