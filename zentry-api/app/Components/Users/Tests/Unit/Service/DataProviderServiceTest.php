<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\Tests\Unit\Traits\DataProvider\HelperTrait as DataProviderHelperTrait;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
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
 * Class DataProviderServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class DataProviderServiceTest extends TestCase
{
    use HelperTrait;
    use DataProviderHelperTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [
                DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR,
                [],
                DataProviderReadonlyContract::STATUS_DISABLED,
            ],
            [
                DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR,
                [],
                DataProviderReadonlyContract::STATUS_ENABLED,
            ],
            [
                DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR,
                [],
                DataProviderReadonlyContract::STATUS_NOT_AUTHORIZED,
            ],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            [$this->randString(), [], DataProviderReadonlyContract::STATUS_ENABLED],
            [DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR, [], $this->randInt()],
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
     * @param array  $config
     * @param int    $status
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
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $driver,
        array $config,
        int $status
    ): void {
        $service = $this->userService__()->dataProviderService();

        $service->create(
            [
                'driver' => $driver,
                'config' => $config,
                'status' => $status,
            ]
        );

        $this->validateStatus($status, $service->readonly());

        self::assertEquals($driver, $service->readonly()->driver());
        self::assertArrayHasKey($driver, DataProviderReadonlyContract::DRIVERS_AVAILABLE);
        self::assertEquals(new Config($config), $service->readonly()->config());

        self::assertCount($service->listRO()->count(), $this->userService__()->readonly()->dataProviders());
        self::assertCount($service->list()->count(), $this->userService__()->readonly()->dataProviders());

        $service->change(['status' => DataProviderReadonlyContract::STATUS_ENABLED]);
        self::assertTrue($service->readonly()->isEnabled());

        $service->change(['status' => DataProviderReadonlyContract::STATUS_DISABLED]);
        self::assertTrue($service->readonly()->isDisabled());

        $dto = $service->dto();

        self::assertEquals($dto->driver, $service->readonly()->driver());

        $service->workWith($dto->id)->remove();

        self::assertCount($service->listRO()->count(), $this->userService__()->readonly()->dataProviders());
        self::assertCount($service->list()->count(), $this->userService__()->readonly()->dataProviders());

        self::assertCount(0, $service->list());
    }

    /**
     * @param string $driver
     * @param array  $config
     * @param int    $status
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $driver,
        array $config,
        int $status
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $storageService = $this->userService__()->storageService();

        $storageService->create(
            [
                'driver' => $driver,
                'status' => $status,
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
