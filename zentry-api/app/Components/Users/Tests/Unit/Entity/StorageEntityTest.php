<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\User\Storage\StorageContract;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\UserContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Convention\ValueObjects\Config\Config;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;

/**
 * Class StorageEntityTest
 */
class StorageEntityTest extends TestCase
{
    use HelperTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [StorageReadonlyContract::DRIVER_DEFAULT, $this->randString(), new Config([])],
            [StorageReadonlyContract::DRIVER_KLOUDLESS_GOOGLE_DRIVE, $this->randString(), new Config([])],
            [StorageReadonlyContract::DRIVER_KLOUDLESS_BOX, $this->randString(), new Config([])],
            [StorageReadonlyContract::DRIVER_KLOUDLESS_DROPBOX, $this->randString(), new Config([])],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            [$this->randString(), $this->randString(), new Config([])],
            [StorageReadonlyContract::DRIVER_KLOUDLESS_GOOGLE_DRIVE, '', new Config([])],
        ];
    }

    /**
     * @param string $driver
     * @param string $name
     * @param Config $config
     *
     * @return void
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws RecursionContextInvalidArgumentException
     * @throws RuntimeException
     * @throws NotFoundException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $driver,
        string $name,
        Config $config
    ): void {
        $identity = $this->generateIdentity();
        $user = $this->createUser();

        $storage = $this->createStorage($user, $identity, $driver, $name, $config);

        $user->addStorage($storage);

        static::assertTrue($storage->identity()->equals($identity));
        static::assertEquals($storage->driver(), $driver);
        static::assertEquals($storage->name(), $name);
        static::assertFalse($storage->enabled());
        static::assertEmpty($storage->used());
        static::assertEmpty($storage->capacity());

        static::assertNotNull($storage->updatedAt());
        static::assertNotNull($storage->createdAt());

        $storage->enable();
        static::assertTrue($storage->enabled());
        $enabledStorage = $user->enabledStorage();

        self::assertTrue($enabledStorage->identity()->equals($storage->identity()));

        $storage->disable();

        $exceptionThrows = false;

        try {
            $user->enabledStorage();
        } catch (NotFoundException $exception) {
            $exceptionThrows = true;
        }

        static::assertTrue($exceptionThrows);

        $newName = $this->randString();

        $storage->changeName($newName);
        self::assertNotEquals($newName, $name);
        self::assertEquals($newName, $storage->name());

        $newUsed = $this->randInt();

        $storage->changeUsed($newUsed);
        self::assertEquals($newUsed, $storage->used());

        $newCapacity = $this->randInt();

        $storage->changeCapacity($newCapacity);
        self::assertEquals($newCapacity, $storage->capacity());
    }

    /**
     * @param string $driver
     * @param string $name
     * @param Config $config
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $driver,
        string $name,
        Config $config
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->createStorage($this->createUser(), IdentityGenerator::next(), $driver, $name, $config);
    }

    /**
     * @return UserContract
     * @throws BindingResolutionException
     */
    private function createUser(): UserContract {
        return $this->app->make(
            UserContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'credentials' =>  new Credentials(new Email($this->randString(). '@mail.com'), new HashedPassword($this->randString())),
            ]
        );
    }

    /**
     * @param UserContract $user
     * @param Identity     $identity
     * @param string       $driver
     * @param string       $name
     * @param Config       $config
     *
     * @return StorageContract
     * @throws BindingResolutionException
     */
    private function createStorage(UserContract $user, Identity $identity, string $driver, string $name, Config $config): StorageContract
    {
        return app()->make(StorageContract::class, [
            'identity' => $identity,
            'user' => $user,
            'driver' => $driver,
            'name' => $name,
            'config' => $config,
        ]);
    }
}
