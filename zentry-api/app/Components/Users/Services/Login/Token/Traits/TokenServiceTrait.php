<?php

namespace App\Components\Users\Services\Login\Token\Traits;

use App\Components\Users\Services\Login\Token\TokenServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait TokenServiceTrait
 *
 * @package App\Components\Users\Services\Login\Token\Traits
 */
trait TokenServiceTrait
{
    /**
     * @var TokenServiceContract | null
     */
    private ?TokenServiceContract $tokenService__ = null;

    /**
     * @return TokenServiceContract
     * @throws BindingResolutionException
     */
    private function tokenService__(): TokenServiceContract
    {
        if (!$this->tokenService__ instanceof TokenServiceContract) {
            $this->tokenService__ = app()->make(TokenServiceContract::class);
        }

        return $this->tokenService__;
    }
}
