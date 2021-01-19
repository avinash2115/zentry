<?php

namespace App\Assistants\QR\Providers;

use App\Assistants\QR\Services\QRService;
use App\Assistants\QR\Services\QRServiceContract;
use App\Providers\BaseServiceProvider;

/**
 * Class QRServiceProvider
 *
 * @package App\Assistants\QR\Providers
 */
class QRServiceProvider extends BaseServiceProvider
{
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
        $this->app->bind(QRServiceContract::class, QRService::class);
    }
}
