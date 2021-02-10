<?php

namespace App\Components\Users\Services\Auth\Traits;

use App\Components\Users\Services\Auth\AuthUserServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait AuthUserServiceTrait
 *
 * @package App\Components\Users\Services\Auth\Traits
 */
trait AuthUserServiceTrait
{
    /**
     * @var AuthUserServiceContract|null
     */
    private ?AuthUserServiceContract $authUserService__ = null;

    /**
     * @return AuthUserServiceContract
     * @throws BindingResolutionException
     */
    private function authUserService__(): AuthUserServiceContract
    {
        if (!$this->authUserService__ instanceof AuthUserServiceContract) {
            $this->authUserService__ = app()->make(AuthUserServiceContract::class);
        }

        return $this->authUserService__;
    }
}
