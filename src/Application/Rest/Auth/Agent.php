<?php

namespace Phalconify\Application\Rest\Auth;

class Agent extends \Phalcon\Di\Injectable
{
    const ERROR_ACCESS_DENIED = 'Access denied.';
    const ERROR_INVALID_CREDENTIALS = 'Invalid auth credentials.';

    const TOKEN_EXPIRY = 3600;

    /**
     * Encryption key.
     *
     * @var string
     */
    public $encryptionKey;

    /**
     * Sets the encryption key to use.
     *
     * @param string $key
     *                    Encryption key
     *
     * @return $this
     */
    public function setEncryptionKey($key = null)
    {
        if ($key) {
            $this->encryptionKey = $key;
        }

        return $this;
    }

    /**
     * Registers the resources for the acl control.
     *
     * @param \Phalcon\Config $endpoints
     *                                   Endpoints to process
     *
     * @return $this
     */
    public function registerResources(\Phalcon\Config $endpoints)
    {
        $acl = $this->getDI()->getShared('phalconify-acl-adapter')->getAdapter();
        foreach ($endpoints as $endpoint) {
            $actions = [];
            foreach ($endpoint->methods as $method) {
                array_push($actions, $method->action);
            }
            $actions = array_unique($actions);
            $acl->addResource(new \Phalcon\Acl\Resource($endpoint->resource), $actions);
        }
        $this->registerRules($endpoints);

        return $this;
    }

    /**
     * Registers the rules for the endpoints.
     *
     * @param \Phalcon\Config $endpoints
     *                                   Endpoints to process
     *
     * @return $this
     */
    public function registerRules(\Phalcon\Config $endpoints)
    {
        $acl = $this->getDI()->getShared('phalconify-acl-adapter')->getAdapter();
        foreach ($endpoints as $endpoint) {
            foreach ($endpoint->methods as $method => $params) {
                $acl->allow((string) $params->auth, $endpoint->resource, (string) $params->action);
            }
        }

        return $this;
    }
}
