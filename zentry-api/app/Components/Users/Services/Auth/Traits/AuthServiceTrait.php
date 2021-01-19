<?php

namespace App\Components\Users\Services\Auth\Traits;

use App\Components\Users\Services\Auth\AuthServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait AuthServiceTrait
 *
 * @package App\Components\Users\Services\Auth\Traits
 */
trait AuthServiceTrait
{
    /**
     * @var AuthServiceContract|null
     */
    private ?AuthServiceContract $authService__ = null;

    /**
     * @return AuthServiceContract
     * @throws BindingResolutionException
     */
    private function authService__(): AuthServiceContract
    {
        if (!$this->authService__ instanceof AuthServiceContract) {
            $this->authService__ = app()->make(AuthServiceContract::class);
        }

        return $this->authService__;
    }
}
