<?php

namespace App\Components\Services\Providers;

use App;
use App\Components\Services\Service\Repository\ServiceRepositoryContract;
use App\Components\Services\Service\Repository\ServiceRepositoryDoctrine;
use App\Components\Services\Service\Repository\ServiceRepositoryMemory;
use App\Components\Services\Service\ServiceContract;
use App\Components\Services\Service\ServiceEntity;
use App\Components\Services\Services\ServiceService;
use App\Components\Services\Services\ServiceServiceContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;

/**
 * Class ServiceServiceProvider
 */
class ServiceServiceProvider extends BaseServiceProvider
{
    private const MODULE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

    /**
     * @var string
     */
    protected string $modulePath = self::MODULE_PATH;

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws PropertyNotInit
     */
    public function boot(): void
    {
        $this->bootMigrations();
        $this->bootRoutes();
        $this->bootCommands();
    }

    /**
     *
     */
    private function bootCommands(): void
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerEntities();
        $this->registerServices();
        $this->registerRepositories();
    }

    /**
     * Register repositories
     *
     * @return void
     */
    protected function registerEntities(): void
    {
        $this->app->bind(
            ServiceContract::class,
            ServiceEntity::class
        );
    }

    /**
     *
     */
    protected function registerServices(): void
    {
        $this->app->bind(
            ServiceServiceContract::class,
            ServiceService::class
        );
    }

    /**
     * Register repositories
     *
     * @return void
     */
    protected function registerRepositories(): void
    {
        if (App::runningUnitTests()) {
            $this->app->singleton(
                ServiceRepositoryContract::class,
                ServiceRepositoryMemory::class
            );
        } else {
            $this->app->singleton(
                ServiceRepositoryContract::class,
                ServiceRepositoryDoctrine::class
            );
        }
    }
}
