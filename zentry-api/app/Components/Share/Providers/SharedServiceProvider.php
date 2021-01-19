<?php

namespace App\Components\Share\Providers;

use App\Components\Share\Services\Shared\SharedResolvedService;
use App\Components\Share\Services\Shared\SharedService;
use App\Components\Share\Services\Shared\SharedServiceContract;
use App\Components\Share\Shared\Repository\SharedRepositoryContract;
use App\Components\Share\Shared\Repository\SharedRepositoryDoctrine;
use App\Components\Share\Shared\Repository\SharedRepositoryMemory;
use App\Components\Share\Shared\SharedContract;
use App\Components\Share\Shared\SharedEntity;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;

/**
 * Class SessionServiceProvider
 */
class SharedServiceProvider extends BaseServiceProvider
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
        $this->bootRoutes();
        $this->bootMigrations();
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
        $this->app->bind(SharedContract::class, SharedEntity::class);
    }

    /**
     *
     */
    protected function registerServices(): void
    {
        $this->app->singleton(SharedResolvedService::class, SharedResolvedService::class);
        $this->app->bind(SharedServiceContract::class, SharedService::class);
    }

    /**
     * Register repositories
     *
     * @return void
     */
    protected function registerRepositories(): void
    {
        if ($this->app->runningUnitTests()) {
            $this->app->singleton(SharedRepositoryContract::class, SharedRepositoryMemory::class);
        } else {
            $this->app->singleton(SharedRepositoryContract::class, SharedRepositoryDoctrine::class);
        }
    }
}
