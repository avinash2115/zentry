<?php

namespace App\Providers;

use App\Console\Commands\Persistence\GenerateODMProxies;
use Arr;
use Doctrine\Common\Cache\VoidCache;
use Doctrine\Common\EventManager;
use Doctrine\MongoDB\Configuration as MongoDBConfiguration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Doctrine\ODM\MongoDB\Types\Type;
use Illuminate\Support\ServiceProvider;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;

/**
 * Class DoctrineODMServiceProvider
 *
 * @package App\Providers
 */
class DoctrineODMServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function register(): void
    {
        $this->registerConsoleCommands();
        $this->registerDocumentManager();
    }

    /**
     *
     */
    public function registerConsoleCommands(): void
    {
        $this->commands(
            [
                GenerateODMProxies::class,
            ]
        );
    }

    /**
     * Setup the entity manager
     */
    protected function registerDocumentManager(): void
    {
        $this->app->singleton(
            DocumentManager::class,
            function ($app) {
                $doctrineODMConfig = config('doctrine_odm');
                $mongoConnection = config('database.connections.' . $doctrineODMConfig['connection']);
                // Setup the Doctrine configuration object
                $documentManagerConfig = $this->getManagerConfig($doctrineODMConfig, $mongoConnection);

                foreach ($doctrineODMConfig['custom_types'] as $name => $class) {
                    Type::registerType($name, $class);
                }

                // Setup the Doctrine connection object
                $connectionConfig = $this->getConnectionConfig($mongoConnection);

                $server = $mongoConnection['host'] . ':' . $mongoConnection['port'];

                if (Arr::get($mongoConnection, 'username') && Arr::get($mongoConnection, 'password')) {
                    $server = $mongoConnection['username'] . ':' . $mongoConnection['password'] . '@' . $server;
                }

                $connection = new Connection(
                    $server, $mongoConnection['options'], $connectionConfig, null, // Event Manager
                    $mongoConnection['driverOptions']
                );

                return DocumentManager::create(
                    $connection,
                    $documentManagerConfig,
                    new EventManager()
                );
            }
        );
    }

    /**
     * @param array $doctrineODMConfig
     * @param array $connectionConfig
     *
     * @return Configuration
     */
    private function getManagerConfig(array $doctrineODMConfig, array $connectionConfig): Configuration
    {
        $documentManagerConfig = new Configuration();
        $documentManagerConfig->setProxyDir($doctrineODMConfig['proxies']['path']);
        $documentManagerConfig->setProxyNamespace($doctrineODMConfig['proxies']['namespace']);
        $documentManagerConfig->setHydratorDir($doctrineODMConfig['hydrators']['path']);
        $documentManagerConfig->setHydratorNamespace($doctrineODMConfig['hydrators']['namespace']);
        $documentManagerConfig->setMetadataDriverImpl(new YamlDriver($doctrineODMConfig['paths']));

        if (Arr::get($doctrineODMConfig, 'filters')) {
            foreach ($doctrineODMConfig['filters'] as $name => $class) {
                $documentManagerConfig->addFilter($name, $class);
            }
        }

        $documentManagerConfig->setDefaultDB($connectionConfig['database']);
        $documentManagerConfig->setMetadataCacheImpl(new VoidCache());

        return $documentManagerConfig;
    }

    /**
     * @param array $mongoConnection
     *
     * @return MongoDBConfiguration
     */
    private function getConnectionConfig(array $mongoConnection): MongoDBConfiguration
    {
        $connectionConfig = new MongoDBConfiguration();
        $connectionConfig->setRetryConnect($mongoConnection['retryConnect']);
        $connectionConfig->setRetryQuery($mongoConnection['retryQuery']);

        return $connectionConfig;
    }
}
