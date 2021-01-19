<?php

namespace App\Components\Users\Services\Device\Traits;

use App\Components\Users\Services\Device\DeviceServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait DeviceServiceTrait
 *
 * @package App\Components\Users\Services\Device\Traits
 */
trait DeviceServiceTrait
{
    /**
     * @var DeviceServiceContract | null
     */
    private ?DeviceServiceContract $deviceService__ = null;

    /**
     * @return DeviceServiceContract
     * @throws BindingResolutionException
     */
    private function deviceService__(): DeviceServiceContract
    {
        if (!$this->deviceService__ instanceof DeviceServiceContract) {
            $this->deviceService__ = app()->make(DeviceServiceContract::class);
        }

        return $this->deviceService__;
    }
}
