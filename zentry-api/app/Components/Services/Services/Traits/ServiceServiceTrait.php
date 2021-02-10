<?php

namespace App\Components\Services\Services\Traits;

use App\Components\Services\Services\ServiceServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait SessionServiceTrait
 *
 * @package App\Components\Services\Services\Session\Traits
 */
trait ServiceServiceTrait
{
    /**
     * @var ServiceServiceContract | null
     */
    private ?ServiceServiceContract $serviceService__ = null;

    /**
     * @return ServiceServiceContract
     * @throws BindingResolutionException
     */
    private function serviceService__(): ServiceServiceContract
    {
        if (!$this->serviceService__ instanceof ServiceServiceContract) {
            $this->serviceService__ = app()->make(ServiceServiceContract::class);
        }

        return $this->serviceService__;
    }
}
