<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\Tests\Unit\Traits\CRMHelperTestTrait;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\ValueObjects\CRM\Config\Config;
use App\Convention\Exceptions\Repository\NotFoundException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;

/**
 * Class CRMEntityTest
 */
class CRMEntityTest extends TestCase
{
    use CRMHelperTestTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [CRMReadonlyContract::DRIVER_THERAPYLOG, new Config([])],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            [$this->randString(), new Config([])],
            ['', new Config([])],
        ];
    }

    /**
     * @param string $driver
     * @param Config $config
     *
     * @return void
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws RecursionContextInvalidArgumentException
     * @throws NotFoundException
     * @throws RuntimeException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $driver,
        Config $config
    ): void {
        $identity = $this->generateIdentity();
        $user = $this->createUser();

        $CRM = $this->createCRM($user, $identity, $driver, $config);

        $user->connectCRM($CRM);

        static::assertTrue($CRM->identity()->equals($identity));
        static::assertEquals($CRM->driver(), $driver);

        static::assertFalse($CRM->active());
        static::assertFalse($CRM->notified());

        static::assertNotNull($CRM->updatedAt());
        static::assertNotNull($CRM->createdAt());

        $CRMByDriver = $user->crmByDriver($driver);
        self::assertTrue($CRMByDriver->identity()->equals($CRM->identity()));

        $CRM->enable();
        static::assertTrue($CRM->active());

        $CRM->disable();
        static::assertFalse($CRM->active());
    }

    /**
     * @param string $driver
     * @param Config $config
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $driver,
        Config $config
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->createCRM($this->createUser(), $this->generateIdentity(), $driver, $config);
    }
}
