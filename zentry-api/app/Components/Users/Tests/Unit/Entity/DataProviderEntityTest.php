<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\Tests\Unit\Traits\DataProvider\HelperTrait as DataProviderHelperTrait;
use App\Components\Users\User\DataProvider\DataProviderContract;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use App\Components\Users\User\UserContract;
use App\Convention\ValueObjects\Config\Config;
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
 * Class DataProviderEntityTest
 */
class DataProviderEntityTest extends TestCase
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
                new Config([]),
                DataProviderReadonlyContract::STATUS_DISABLED,
            ],
            [
                DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR,
                new Config([]),
                DataProviderReadonlyContract::STATUS_ENABLED,
            ],
            [
                DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR,
                new Config([]),
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
            [$this->randString(), new Config([]), DataProviderReadonlyContract::STATUS_ENABLED],
            [DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR, new Config([]), $this->randInt()],
        ];
    }

    /**
     * @param string $driver
     * @param Config $config
     * @param int    $status
     *
     * @return void
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     * @throws RuntimeException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $driver,
        Config $config,
        int $status
    ): void {
        $identity = $this->generateIdentity();
        $user = $this->createUser();

        $entity = $this->make($user, $identity, $driver, $config, $status);
        $user->addDataProvider($entity);

        static::assertTrue($entity->identity()->equals($identity));
        static::assertEquals($entity->driver(), $driver);
        static::assertEquals($entity->status(), $status);

        static::assertNotNull($entity->updatedAt());
        static::assertNotNull($entity->createdAt());

        $this->validateStatus($status, $entity);
    }

    /**
     * @param string $driver
     * @param Config $config
     * @param int    $status
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $driver,
        Config $config,
        int $status
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->make($this->createUser(), IdentityGenerator::next(), $driver, $config, $status);
    }

    /**
     * @param UserContract $user
     * @param Identity     $identity
     * @param string       $driver
     * @param Config       $config
     * @param int          $status
     *
     * @return DataProviderContract
     * @throws BindingResolutionException
     */
    private function make(
        UserContract $user,
        Identity $identity,
        string $driver,
        Config $config,
        int $status
    ): DataProviderContract {
        return app()->make(
            DataProviderContract::class,
            [
                'identity' => $identity,
                'user' => $user,
                'driver' => $driver,
                'config' => $config,
                'status' => $status,
            ]
        );
    }
}
