<?php

namespace Phalconify\Application\Rest\Auth;

class Middleware extends \Phalcon\Di\Injectable
{
    private function _getCurrentHandlers(\Phalcon\Mvc\Micro $app)
    {
        $activeHandlers = $app->getActiveHandler();
        if (!is_array($activeHandlers)) {
            return false;
        }

        $activeHandlers = array_values($activeHandlers);
        $controllerWithNamespace = strtolower(get_class($activeHandlers[0]));
        $controller = substr($controllerWithNamespace, strrpos($controllerWithNamespace, '\\') + 1);
        $controller = ($controller != 'controller') ? str_replace('controller', '', $controller) : $controller;
        $action = $activeHandlers[1];

        return [
            'controller' => $controller,
            'action' => $action,
        ];
    }

    public function isAllowed(\Phalcon\Mvc\Micro $app, \Phalcon\Acl\Adapter\Memory $acl, $role = null)
    {
        // If options request, pass
        $request = $this->getDI()->get('phalconify-request');
        if ($request->isMethod('OPTIONS') === true) {
            return true;
        }

        // Get current handlers
        $currentHandlers = $this->_getCurrentHandlers($app);

        // Check role
        if ($role === null) {
            return false;
        }

        return $acl->isAllowed($role, $currentHandlers['controller'], $currentHandlers['action']);
    }
}
