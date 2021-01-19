<?php

namespace App\Components\Users\Tests\Unit\Repository;

use App\Components\Users\User\Repository\UserRepositoryDoctrine;
use App\Components\Users\User\Storage\StorageContract;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\UserContract;
use App\Convention\ValueObjects\Config\Config;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use Hash;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Tests\TestCase;

/**
 * Class UserRepositoryTest
 *
 * @package App\Components\Users\Tests\Unit\Repository
 */
class UserRepositoryTest extends TestCase
{
    use HelperTrait;
    use RefreshDatabase;

    /**
     * @var UserRepositoryDoctrine|null
     */
    private ?UserRepositoryDoctrine $repository = null;

    /**
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testPersist(): void
    {
        $user = $this->createUser();
        $this->addStorages($user);

        $this->repository()->persist($user);
        $this->flush();

        /**
         * @var UserContract $userFromStorage
         */
        $userFromStorage = $this->repository()->byIdentity($user->identity());

        static::assertEquals($userFromStorage->identity(), $user->identity());

        $this->assertEquals(
            Hash::check($user->password(), $userFromStorage->password()),
            Hash::check($user->password(), $user->password())
        );

        $user->storages()->each(
            static function (StorageContract $storage) use ($userFromStorage) {
                $persistedStorage = $userFromStorage->storages()->first(
                    function (StorageContract $persistedStorage) use ($storage) {
                        return $persistedStorage->identity()->equals($storage->identity());
                    }
                );

                if (!$persistedStorage instanceof StorageContract) {
                    throw new RuntimeException();
                }

                self::assertInstanceOf(StorageContract::class, $persistedStorage);
                static::assertTrue($storage->identity()->equals($persistedStorage->identity()));
                static::assertEquals($storage->driver(), $persistedStorage->driver());
                static::assertEquals($storage->name(), $persistedStorage->name());
                static::assertEquals($storage->enabled(), $persistedStorage->enabled());
                static::assertEquals($storage->used(), $persistedStorage->used());
                static::assertEquals($storage->capacity(), $persistedStorage->capacity());

                static::assertEquals($storage->updatedAt(),  $persistedStorage->updatedAt());
                static::assertEquals($storage->createdAt(),  $persistedStorage->createdAt());
            }
        );
        static::assertCount(1, $this->repository()->getAll());
        static::assertCount(1, $this->repository()->filterByEmails([$user->email()])->getAll());
        static::assertCount(1, $this->repository()->filterByIds([$user->identity()])->getAll());
        static::assertCount(0, $this->repository()->filterByIds([$this->generateIdentity()->toString()])->getAll());
        $this->repository()->destroy($user);
        $this->flush();
        static::assertCount(0, $this->repository()->getAll());
    }

    /**
     * Check by identity exception
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testByIdentityException(): void
    {
        try {
            $this->repository()->byIdentity(IdentityGenerator::next());
            static::assertTrue(false);
        } catch (NotFoundException $e) {
            static::assertInstanceOf(NotFoundException::class, $e);
        }
    }

    /**
     * @param UserContract $user
     *
     * @throws BindingResolutionException
     * @throws RuntimeException
     */
    private function addStorages(UserContract $user): void
    {
        $drivers = array_merge(StorageReadonlyContract::KLOUDLESS_GROUP, [StorageReadonlyContract::DRIVER_DEFAULT]);

        foreach ($drivers as $driver) {
            $user->addStorage(
                app()->make(
                    StorageContract::class,
                    [
                        'identity' => IdentityGenerator::next(),
                        'user' => $user,
                        'driver' => $driver,
                        'name' => StorageReadonlyContract::AVAILABLE_DRIVERS[$driver],
                        'config' => new Config([]),
                    ]
                )
            );
        }
    }

    /**
     * @return UserRepositoryDoctrine
     * @throws BindingResolutionException
     */
    private function repository(): UserRepositoryDoctrine
    {
        if (!$this->repository instanceof UserRepositoryDoctrine) {
            $this->repository = app()->make(UserRepositoryDoctrine::class);
        }

        return $this->repository;
    }
}
