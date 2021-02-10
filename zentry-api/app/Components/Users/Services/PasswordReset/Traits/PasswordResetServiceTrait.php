<?php

namespace App\Components\Users\Services\PasswordReset\Traits;

use App\Components\Users\Services\PasswordReset\PasswordResetServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait PasswordResetServiceTrait
 *
 * @package App\Components\Users\Services\PasswordReset\Traits
 */
trait PasswordResetServiceTrait
{
    /**
     * @var PasswordResetServiceContract | null
     */
    private ?PasswordResetServiceContract $passwordResetService__ = null;

    /**
     * @return PasswordResetServiceContract
     * @throws BindingResolutionException
     */
    private function passwordResetService__(): PasswordResetServiceContract
    {
        if (!$this->passwordResetService__ instanceof PasswordResetServiceContract) {
            $this->passwordResetService__ = app()->make(PasswordResetServiceContract::class);
        }

        return $this->passwordResetService__;
    }
}
