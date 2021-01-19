<?php

namespace App\Assistants\Elastic\Providers;

use App\Assistants\Elastic\Console\Generators\Base\Indexing\Index;
use App\Assistants\Elastic\Console\Generators\Filter\Setup;
use App\Assistants\Elastic\Services\ElasticMemoryService;
use App\Assistants\Elastic\Services\ElasticService;
use App\Assistants\Elastic\Services\ElasticServiceContract;
use App\Assistants\Elastic\Services\Setup\FilterService;
use App\Assistants\Elastic\Services\Setup\FilterServiceContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;

/**
 * Class ElasticProvider
 *
 * @package App\Assistants\Elastic\Providers
 */
class ElasticProvider extends BaseServiceProvider
{
    public const MODULE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

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
        $this->bootConfigs('elasticsearch');
        $this->bootCommands();
    }

    /**
     *
     */
    private function bootCommands(): void
    {
        $this->commands(
            [
                Index::class,
                Setup::class,
            ]
        );
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->registerServices();
    }

    /**
     * @return void
     */
    protected function registerServices(): void
    {
        if ($this->app->runningUnitTests()) {
            $this->app->bind(ElasticServiceContract::class, ElasticMemoryService::class);
        } else {
            $this->app->bind(ElasticServiceContract::class, ElasticService::class);
        }

        $this->app->bind(FilterServiceContract::class, FilterService::class);
    }
}
