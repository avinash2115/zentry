<?php

namespace App\Components\Users\Tests\Unit\Repository;

use App\Components\Users\PasswordReset\PasswordResetContract;
use App\Components\Users\PasswordReset\Repository\PasswordResetRepositoryDoctrine;
use App\Components\Users\User\Repository\UserRepositoryDoctrine;
use App\Components\Users\User\UserContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class PasswordResetPersistenceTest
 *
 * @package App\Components\Users\Tests\Unit\Repository
 */
class PasswordResetRepositoryTest extends TestCase
{
    use HelperTrait;
    use RefreshDatabase;

    /**
     * @var UserRepositoryDoctrine|null
     */
    private ?UserRepositoryDoctrine $userRepository = null;

    /**
     * @var PasswordResetRepositoryDoctrine|null
     */
    private ?PasswordResetRepositoryDoctrine $passwordResetRepository = null;

    /**
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function testPersist(): void
    {
        $this->refreshTestDatabase();

        $passwordReset = $this->createPasswordReset();
        $this->passwordResetRepository()->persist($passwordReset);
        $this->flush();
        $persistedRP = $this->passwordResetRepository()->byIdentity($passwordReset->identity());

        static::assertEquals($persistedRP->identity(), $passwordReset->identity());
        static::assertEquals($persistedRP->user(), $passwordReset->user());
        static::assertFalse($persistedRP->isExpired());

        static::assertCount(1, $this->passwordResetRepository()->getAll());
        $this->passwordResetRepository()->destroy($passwordReset);
        $this->flush();
        static::assertCount(0, $this->passwordResetRepository()->getAll());

        $this->userRepository()->getAll()->each(
            function (UserContract $user) {
                $this->userRepository()->destroy($user);
                $this->flush();
            }
        );
    }

    /**
     * Check by identity exception
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function testByIdentityException(): void
    {
        try {
            $this->passwordResetRepository()->byIdentity(IdentityGenerator::next());
            $this->assertTrue(false);
        } catch (NotFoundException $e) {
            $this->assertInstanceOf(NotFoundException::class, $e);
        }
    }

    /**
     * @return UserRepositoryDoctrine
     * @throws BindingResolutionException
     */
    private function userRepository(): UserRepositoryDoctrine
    {
        if (!$this->userRepository instanceof UserRepositoryDoctrine) {
            $this->userRepository = app()->make(UserRepositoryDoctrine::class);
        }

        return $this->userRepository;
    }

    /**
     * @return PasswordResetRepositoryDoctrine
     * @throws BindingResolutionException
     */
    private function passwordResetRepository(): PasswordResetRepositoryDoctrine
    {
        if (!$this->passwordResetRepository instanceof PasswordResetRepositoryDoctrine) {
            $this->passwordResetRepository = app()->make(PasswordResetRepositoryDoctrine::class);
        }

        return $this->passwordResetRepository;
    }

    /**
     * @return PasswordResetContract
     * @throws BindingResolutionException
     */
    private function createPasswordReset(): PasswordResetContract
    {
        $user = $this->createUser();

        $this->userRepository()->persist($user);

        return $this->app->make(
            PasswordResetContract::class,
            [
                'identity' => $this->generateIdentity(),
                'user' => $user,
            ]
        );
    }
}