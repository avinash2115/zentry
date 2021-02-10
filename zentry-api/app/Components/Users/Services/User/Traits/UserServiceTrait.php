<?php

namespace App\Components\Users\Services\User\Traits;

use App\Components\Users\Services\User\UserServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait UserServiceTrait
 *
 * @package App\Components\Users\Services\User\Traits
 */
trait UserServiceTrait
{
    /**
     * @var UserServiceContract|null
     */
    protected ?UserServiceContract $userService__ = null;

    /**
     * @return UserServiceContract
     * @throws BindingResolutionException
     */
    protected function userService__(): UserServiceContract
    {
        if (!$this->userService__ instanceof UserServiceContract) {
            $this->userService__ = app()->make(UserServiceContract::class);
        }

        return $this->userService__;
    }
}
