<?php

namespace Phalconify\Utils;

use Phalcon\Config;

/**
 * Implements utilities related to the database.
 */
trait Database
{
    /**
     * Gets the configuration for a database from a configuration object by a given name or default singular
     * @param Config $databaseConfig
     * @param string $name
     * @return array|bool|mixed|Config|string
     */
    public function getDbConnectionFromConfigByName(Config $config, string $name)
    {
        $config = (array)$config;

        $singleConnection = false;
        if (isset($config['adapter'])) {
            $singleConnection = true;
        }
        if ($singleConnection) {
            return $config;
        }
        foreach ($config as $connection) {
            if ($connection->serviceName === $name) {
                return $connection;
            }
        }

        return false;
    }

    /**
     * Creates an instance of MongoDB\Driver\Manager based on provided config
     * @param Config $config
     * @return \MongoDB\Driver\Manager
     */
    public function getMongoManagerInstance(Config $config)
    {
        $dbUrl = 'mongodb://' . $config->username . ':' . $config->password . '@' . $config->host . ':' . $config->port . '/' . $config->name;

        return new \MongoDB\Driver\Manager($dbUrl);
    }
}