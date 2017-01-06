<?php

namespace Phalconify\Application\Rest\Auth;

use Phalconify\Application\Rest\Collections\Users;
use Phalcon\Acl\Adapter\Memory;

/**
 * User related access control list.
 */
class ACL
{
    /**
     * Adapter to use.
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor of the class.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct()
    {
        $this->adapter = new Memory();
        $this->adapter->setDefaultAction(\Phalcon\Acl::DENY);
        $this->registerRoles();
    }

    /**
     * Returns the adapter for the ACL being used.
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Registers roles into the ACL.
     */
    protected function registerRoles()
    {
        $this->adapter->addRole(new \Phalcon\Acl\Role(Users::ROLE_GUEST));
        $this->adapter->addRole(new \Phalcon\Acl\Role(Users::ROLE_USER), Users::ROLE_GUEST);
        $this->adapter->addRole(new \Phalcon\Acl\Role(Users::ROLE_ADMIN), Users::ROLE_USER);
    }
}
