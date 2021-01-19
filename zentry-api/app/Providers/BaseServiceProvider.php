<?php

namespace App\Providers;

use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Support\ServiceProvider;

/**
 * Class BaseServiceProvider
 *
 * @package App\Providers
 */
class BaseServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected string $modulePath;

    /**
     * Boot configs
     *
     * @param string $name
     * @param string $filename
     *
     * @return void
     * @throws PropertyNotInit
     */
    protected function bootConfigs(string $name, string $filename = 'config.php'): void
    {
        $this->mergeConfigRecursiveFrom($this->_modulePath() . 'Config' . DIRECTORY_SEPARATOR . $filename, $name);
    }

    /**
     * Boot routes
     *
     * @return void
     * @throws PropertyNotInit
     */
    protected function bootRoutes(): void
    {
        $this->loadRoutesFrom($this->_modulePath() . 'Routes' . DIRECTORY_SEPARATOR . 'routes.php');
    }

    /**
     *
     */
    protected function bootMigrations(): void
    {
        $this->loadMigrationsFrom($this->_modulePath() . 'Migrations');
    }

    /**
     * Boot channels
     *
     * @return void
     * @throws PropertyNotInit
     */
    protected function bootChannels(): void
    {
        require $this->_modulePath() . 'Routes' . DIRECTORY_SEPARATOR . 'channels.php';
    }

    /**
     * @return string
     * @throws PropertyNotInit
     */
    private function _modulePath(): string
    {
        if (!is_string($this->modulePath)) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->modulePath;
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param string $path
     * @param string $key
     *
     * @return void
     */
    protected function mergeConfigRecursiveFrom($path, $key): void
    {
        $config = config()->get($key, []);

        config()->set($key, array_merge_recursive(require $path, $config));
    }
}
