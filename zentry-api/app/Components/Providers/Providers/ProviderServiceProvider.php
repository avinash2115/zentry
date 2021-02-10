<?php

namespace App\Components\Providers\Providers;

use App;
use App\Components\Providers\ProviderService\Repository\ProviderRepositoryContract;
use App\Components\Providers\ProviderService\Repository\ProviderRepositoryDoctrine;
use App\Components\Providers\ProviderService\Repository\ProviderRepositoryMemory;
use App\Components\Providers\ProviderService\ProviderContract;
use App\Components\Providers\ProviderService\ProviderEntity;
use App\Components\Providers\ProviderServices\ProviderService;
use App\Components\Providers\ProviderServices\ProviderServiceContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;

/**
 * Class ProviderServiceProvider
 */
class ProviderServiceProvider extends BaseServiceProvider
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
            ProviderContract::class,
            ProviderEntity::class
        );
    }

    /**
     *
     */
    protected function registerServices(): void
    {
        $this->app->bind(
            ProviderServiceContract::class,
            ProviderService::class
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
                ProviderRepositoryContract::class,
                ProviderRepositoryMemory::class
            );
        } else {
            $this->app->singleton(
                ProviderRepositoryContract::class,
                ProviderRepositoryDoctrine::class
            );
        }
    }
}
