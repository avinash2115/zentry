<?php

namespace App\Components\Share\Services\Shared\Traits;

use App\Components\Share\Services\Shared\SharedServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait SharedServiceTrait
 *
 * @package App\Components\Share\Services\Shared\Traits
 */
trait SharedServiceTrait
{
    /**
     * @var SharedServiceContract | null
     */
    private ?SharedServiceContract $sharedService__ = null;

    /**
     * @return SharedServiceContract
     * @throws BindingResolutionException
     */
    private function sharedService__(): SharedServiceContract
    {
        if (!$this->sharedService__ instanceof SharedServiceContract) {
            $this->sharedService__ = app()->make(SharedServiceContract::class);
        }

        return $this->sharedService__;
    }
}
