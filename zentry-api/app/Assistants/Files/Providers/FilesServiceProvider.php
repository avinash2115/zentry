<?php

namespace App\Assistants\Files\Providers;

use App\Assistants\Files\Services\FileService;
use App\Assistants\Files\Services\FileServiceContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;

/**
 * Class FilesServiceProvider
 *
 * @package App\Assistants\Files\Providers
 */
class FilesServiceProvider extends BaseServiceProvider
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
        $this->bootConfigs('files');
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
     * Register mutators
     *
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->bind(FileServiceContract::class, FileService::class);
    }
}
