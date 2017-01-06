<?php

namespace Phalconify\Application\Rest\Auth;

use Phalcon\Config;
use Phalcon\Di\Injectable;
use Phalconify\Application\Rest\Collections\Users;

/**
 * Creates the authorization mechanism in the system.
 */
class Authorise extends Injectable
{
    /**
     * Constructor of the class.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Initializes the authorization process.
     */
    public function initialize()
    {
        $this->setUser();
        $this->setToken();
        $this->setAgent();
    }

    /**
     * Gets the current user accessing the system.
     *
     * @staticvar ArmonB\User|boolean $user
     *
     * @return ArmonB\User|bool
     *                          User logged in in the system if exists, false otherwise
     */
    public function getCurrentUser()
    {
        static $user;
        if (!isset($user)) {
            $config = $this->getDI()->getShared('phalconify-config');
            $user = Users::getUser($config->encryption->key);
        }

        return $user;
    }

    /**
     * Sets a user into the dependency injector object.
     */
    protected function setUser()
    {
        $user = $this->getCurrentUser();
        $this->getDI()->set('phalconify-user', function () use ($user) {
            return $user;
        }, true);
    }

    protected function setAgent()
    {
        $user = $this->getCurrentUser();
        $config = $this->getDI()->getShared('phalconify-config');
        $authAgent = $this->getDI()->getShared('phalconify-auth-agent');
        $authAgent->setEncryptionKey($config->encryption->key);

        $app = $this->getDI()->getShared('phalconify-application');
        $authAdapter = $this->getDI()->getShared('phalconify-acl-adapter');
        $role = ($user !== false ? $user->getRole() : Users::ROLE_GUEST);
        $app->before(function () use ($app, $authAdapter, $role) {
            $middleware = $this->getDI()->get('phalconify-auth-middleware');

            return $middleware->isAllowed($app, $authAdapter->getAdapter(), $role);
        });
    }

    /**
     * Sets controls on the endpoints to check the access.
     *
     * @param Config $endpoints
     *                          Endpoints to control
     */
    public function setControl(Config $endpoints)
    {
        $authAgent = $this->getDI()->getShared('phalconify-auth-agent');
        $authAgent->registerResources($endpoints);
    }

    /**
     * Sets the token for the current user.
     */
    protected function setToken()
    {
        $user = $this->getCurrentUser();

        return ($user) ? $user->updateTokenExpiry() : false;
    }
}
