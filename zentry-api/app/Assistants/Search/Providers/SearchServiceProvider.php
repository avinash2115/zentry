<?php

namespace App\Assistants\Search\Providers;

use App\Assistants\Search\Services\SearchService;
use App\Assistants\Search\Services\SearchServiceContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;

/**
 * Class SearchServiceProvider
 *
 * @package App\Assistants\Search\Providers
 */
class SearchServiceProvider extends BaseServiceProvider
{
    const MODULE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerServices();
    }

    /**
     * Register services
     *
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->singleton(SearchServiceContract::class, SearchService::class);
    }
}
