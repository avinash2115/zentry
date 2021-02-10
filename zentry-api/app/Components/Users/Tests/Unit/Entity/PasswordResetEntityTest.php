<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\PasswordReset\PasswordResetContract;
use App\Components\Users\User\UserContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

/**
 * Class PasswordResetEntityTest
 *
 * @package App\Components\Users\Tests\Unit\Entity
 */
class PasswordResetEntityTest extends TestCase
{
    use HelperTrait;

    /**
     * @throws BindingResolutionException
     */
    public function testCreateCorrect(): void
    {
        $identity = $this->generateIdentity();
        $user = $this->createUser();
        $passwordReset = $this->createPasswordReset($identity, $user);

        static::assertTrue($passwordReset->identity()->equals($identity));

        static::assertEquals($user, $passwordReset->user());
        static::assertEquals($user->identity(), $passwordReset->user()->identity());

        static::assertFalse($passwordReset->isExpired());
        static::assertNotNull($passwordReset->createdAt());
        static::assertNotNull($passwordReset->updatedAt());

        static::assertNotNull($user->updatedAt());
        static::assertNotNull($user->createdAt());
    }

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     *
     * @return PasswordResetContract
     * @throws BindingResolutionException
     */
    private function createPasswordReset(Identity $identity, UserReadonlyContract $user): PasswordResetContract
    {
        return $this->app->make(
            PasswordResetContract::class,
            [
                'identity' => $identity,
                'user' => $user,
            ]
        );
    }
}