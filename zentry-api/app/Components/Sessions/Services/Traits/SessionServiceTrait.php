<?php

namespace App\Components\Sessions\Services\Traits;

use App\Components\Sessions\Services\SessionServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait SessionServiceTrait
 *
 * @package App\Components\Sessions\Services\Session\Traits
 */
trait SessionServiceTrait
{
    /**
     * @var SessionServiceContract | null
     */
    private ?SessionServiceContract $sessionService__ = null;

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException
     */
    private function sessionService__(): SessionServiceContract
    {
        if (!$this->sessionService__ instanceof SessionServiceContract) {
            $this->sessionService__ = app()->make(SessionServiceContract::class);
        }

        return $this->sessionService__;
    }
}
