<?php

namespace App\Assistants\CRM\Providers;

use App\Assistants\CRM\Services\CRMService;
use App\Assistants\CRM\Services\CRMServiceContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;

/**
 * Class CRMServiceProvider
 *
 * @package App\Assistants\CRM\Providers
 */
class CRMServiceProvider extends BaseServiceProvider
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
        $this->bootConfigs('crm');
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
        $this->app->bind(CRMServiceContract::class, CRMService::class);
    }
}
