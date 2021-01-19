<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\Services\User\UserServiceContract;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\UserDTO;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Tests\Traits\HelperTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;

/**
 * Class UserServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class UserServiceTest extends TestCase
{
    use HelperTrait;

    public const EMAIL = 'test@test.com';

    /**
     * Check on create user
     *
     * @return UserServiceContract
     * @throws Exception
     */
    public function testCreate(): UserServiceContract
    {
        $profilePayload = new Payload($this->randString(), $this->randString());

        $userService = $this->createUser()->attachProfile($profilePayload);

        $userDto = $userService->dto();

        $this->checkDTO($userDto, $profilePayload);

        self::assertInstanceOf(StorageReadonlyContract::class, $userService->readonly()->enabledStorage());
        self::assertTrue($userService->readonly()->enabledStorage()->isDriver(StorageReadonlyContract::DRIVER_DEFAULT));

        return $userService;
    }

    /**
     * Add new user to service
     *
     * @return UserServiceContract
     * @throws Exception
     */
    private function createUser(): UserServiceContract
    {
        $password = $this->randString();

        return $this->userService__()->create(
            new Credentials(new Email(self::EMAIL), new HashedPassword($password), new HashedPassword($password))
        );
    }

    /**
     * Check user dto
     *
     * @param UserDTO $userDto
     * @param Payload $payload
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws PHPUnitException
     * @throws RecursionContextInvalidArgumentException
     */
    private function checkDTO(UserDTO $userDto, Payload $payload): void
    {
        static::assertInstanceOf(UserDTO::class, $userDto);
        static::assertEquals(self::EMAIL, $userDto->email);
        static::assertNotNull($userDto->createdAt);
        static::assertNotNull($userDto->updatedAt);

        static::assertEquals($payload->firstName(), $userDto->profileDTO->firstName);
        static::assertEquals($payload->lastName(), $userDto->profileDTO->lastName);
        static::assertEquals($payload->phoneCode(), $userDto->profileDTO->phoneCode);
        static::assertEquals($payload->phoneNumber(), $userDto->profileDTO->phoneNumber);
    }

    /**
     * Check work with method
     *
     * @param UserServiceContract $userService
     *
     * @return UserServiceContract
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws PropertyNotInit
     * @throws RecursionContextInvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     * @throws Exception
     * @depends testCreate
     */
    public function testChange(UserServiceContract $userService): UserServiceContract
    {
        $newEmail = "{$this->randString()}@zentry.com";
        $userService->change(['email' => $newEmail]);

        $profilePayload = new Payload($this->randString(), $this->randString(), '1', $this->randInt());
        $userService->changeProfile($profilePayload);

        $profile = $userService->profileDTO();

        static::assertEquals($profilePayload->firstName(), $profile->firstName);
        static::assertEquals($profilePayload->lastName(), $profile->lastName);
        static::assertEquals($profilePayload->phoneCode(), $profile->phoneCode);
        static::assertEquals($profilePayload->phoneNumber(), $profile->phoneNumber);

        self::assertEquals($newEmail, $userService->readonly()->email());

        return $userService;
    }

    /**
     * Check work with method
     *
     * @param UserServiceContract $userService
     *
     * @return UserServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     * @depends testChange
     */
    public function testWorkWith(UserServiceContract $userService): UserServiceContract
    {
        $userDto = $userService->dto();

        $userDtoOriginal = $userService->workWith($userDto->id)->dto();
        static::assertEquals($userDto, $userDtoOriginal);

        return $userService;
    }

    /**
     * @param UserServiceContract $userService
     *
     * @return UserServiceContract
     * @throws ExpectationFailedException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RecursionContextInvalidArgumentException
     * @depends testWorkWith
     */
    public function testArchiveRestore(UserServiceContract $userService): UserServiceContract
    {
        $userService->archive();
        static::assertNotNull($userService->readonly()->archivedAt());
        $userService->restore();
        static::assertNull($userService->readonly()->archivedAt());

        return $userService;
    }

    /**
     * Check work with method exception
     *
     * @param UserServiceContract $userService
     *
     * @throws BindingResolutionException|NotFoundException
     * @depends testArchiveRestore
     */
    public function testWorkWithException(UserServiceContract $userService): void
    {
        $this->expectException(NotFoundException::class);
        $userService->workWith($this->generateIdentity());
    }
}
