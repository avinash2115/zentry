<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\ValueObjects\Config\Config;
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
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;

/**
 * Class StorageServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class StorageServiceTest extends TestCase
{
    use HelperTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [StorageReadonlyContract::DRIVER_KLOUDLESS_GOOGLE_DRIVE, $this->randInt(), $this->randInt(), []],
            [StorageReadonlyContract::DRIVER_KLOUDLESS_BOX, $this->randInt(), $this->randInt(), []],
            [StorageReadonlyContract::DRIVER_KLOUDLESS_DROPBOX, $this->randInt(), $this->randInt(), []],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            [$this->randString(), []],
        ];
    }

    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     */
    public function testWorkWithException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->userService__()->storageService()->workWith((string)$this->generateIdentity());
    }

    /**
     * @param string $driver
     * @param int    $used
     * @param int    $capacity
     * @param array  $config
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PHPUnitException
     * @throws PropertyNotInit
     * @throws RecursionContextInvalidArgumentException
     * @throws RuntimeException
     *
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $driver,
        int $used,
        int $capacity,
        array $config
    ): void {
        $storageService = $this->userService__()->storageService();

        $storageService->create(
            [
                'driver' => $driver,
                'config' => $config,
            ]
        );

        self::assertEquals($driver, $storageService->readonly()->driver());
        self::assertEquals(StorageReadonlyContract::AVAILABLE_DRIVERS[$driver], $storageService->readonly()->name());
        self::assertEquals(new Config($config), $storageService->readonly()->config());

        self::assertCount($storageService->listRO()->count(), $this->userService__()->readonly()->storages());
        self::assertCount($storageService->list()->count(), $this->userService__()->readonly()->storages());
        self::assertTrue(
            $this->userService__()->readonly()->enabledStorage()->isDriver(StorageReadonlyContract::DRIVER_DEFAULT)
        );

        $defaultStorage = $this->userService__()->readonly()->enabledStorage();

        $storageService->change(['enabled' => true]);
        self::assertFalse(
            $this->userService__()->readonly()->enabledStorage()->isDriver(StorageReadonlyContract::DRIVER_DEFAULT)
        );
        self::assertTrue($this->userService__()->readonly()->enabledStorage()->isDriver($driver));

        $storageService->change(['used' => $used]);
        $storageService->change(['capacity' => $capacity]);

        self::assertEquals($used, $storageService->readonly()->used());
        self::assertEquals($capacity, $storageService->readonly()->capacity());

        $dto = $storageService->dto();

        self::assertEquals($dto->driver, $storageService->readonly()->driver());
        self::assertEquals($dto->name, $storageService->readonly()->name());
        self::assertEquals($dto->used, $storageService->readonly()->used());
        self::assertEquals($dto->capacity, $storageService->readonly()->capacity());

        $enabledErrorIsThrown = false;

        try {
            $storageService->remove();
        } catch (RuntimeException $exception) {
            $enabledErrorIsThrown = true;
        }

        self::assertTrue($enabledErrorIsThrown);

        $defaultErrorIsThrown = false;

        try {
            $storageService->workWith($defaultStorage->identity())->remove();
        } catch (RuntimeException $exception) {
            $defaultErrorIsThrown = true;
        }

        self::assertTrue($defaultErrorIsThrown);

        $storageService->change(['enabled' => true]);
        $storageService->workWith($dto->id)->remove();

    }

    /**
     * @param string $driver
     * @param array  $config
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws PropertyNotInit
     * @throws RuntimeException
     *
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $driver,
        array $config
    ): void
    {
        $this->expectException(InvalidArgumentException::class);

        $storageService = $this->userService__()->storageService();

        $storageService->create(
            [
                'driver' => $driver,
                'config' => $config,
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $password = $this->randString();

        $this->userService__()->create(
            new Credentials(
                new Email($this->randString() . '@mail.com'),
                new HashedPassword($password),
                new HashedPassword($password)
            )
        )->attachProfile(new Payload($this->randString(), $this->randString()));
    }
}
