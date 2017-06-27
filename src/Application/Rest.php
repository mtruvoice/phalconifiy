<?php

namespace Phalconify\Application;

class Rest extends ApplicationBase implements ApplicationInterface
{
    public function initialise()
    {
        // Setup database connection
        $this->setDatabaseConnection();

        // Load rest specific services
        $this->loadServices(__DIR__.'/Rest/Config/services.json');

        // Load endpoints from config
        $customEndpoints = $this->loadEndpoints(CONFIG_DIR.'/endpoints.json');

        // If authorisation is enabled load auth services and endpoints
        if ($this->getDI()->getShared('phalconify-config')->application->authorisation) {
            $this->loadServices(__DIR__.'/Rest/Auth/Config/services.json');
            $this->loadEndpoints(__DIR__.'/Rest/Auth/Config/endpoints.json');

            // Enable authorisation on the user endpoints
            $authoriser = $this->di->getShared('phalconify-auth-authorise');
            $authoriser->setControl(new \Phalcon\Config\Adapter\Json(__DIR__.'/Rest/Auth/Config/endpoints.json'));

            // Enable authorisation on the custom endpoints
            $authoriser->setControl($customEndpoints);
        }
    }

    public function loadEndpoints($filePath = null)
    {
        $endpoints = new \Phalcon\Config\Adapter\Json($filePath);

        // Initialise handlers
        $handlers = [];

        // Load each endpoint collection
        foreach ($endpoints as $endpoint) {
            // Get a handler
            if (array_key_exists($endpoint->resource, $handlers)) {
                $handler = $handlers[$endpoint->resource];
            } else {
                $handler = new $endpoint->controller();
                $handlers[$endpoint->resource] = $handler;
            }

            $createCollection = new \Phalcon\Mvc\Micro\Collection();
            $createCollection->setHandler($handler);

            $createCollection->setPrefix($endpoint->prefix);
            foreach ($endpoint->methods as $method => $params) {
                if ($params) {
                    $createCollection->$method('/', $params->action);
                }
            }
            $this->mount($createCollection);
        }

        return $endpoints;
    }

    public function setNotFoundHandler()
    {
        $this->notFound(function () {
            return \Phalconify\Application\Rest\Http\Response\Json::notFound(['endpoint not found']);
        });
    }
}
