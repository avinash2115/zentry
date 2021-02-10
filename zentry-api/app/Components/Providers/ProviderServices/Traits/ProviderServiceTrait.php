<?php

namespace App\Components\Providers\ProviderServices\Traits;

use App\Components\Providers\ProviderServices\ProviderServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait SessionServiceTrait
 *
 * @package App\Components\Providers\ProviderServices\Session\Traits
 */
trait ProviderServiceTrait
{
    /**
     * @var ProviderServiceContract | null
     */
    private ?ProviderServiceContract $providerService__ = null;

    /**
     * @return ProviderServiceContract
     * @throws BindingResolutionException
     */
    private function providerService__(): ProviderServiceContract
    {
        if (!$this->providerService__ instanceof ProviderServiceContract) {
            $this->providerService__ = app()->make(ProviderServiceContract::class);
        }

        return $this->providerService__;
    }
}
