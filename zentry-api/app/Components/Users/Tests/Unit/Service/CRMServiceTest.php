<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\Events\CRM\Connection\Lost as EventLost;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\CRM\Config\Config;
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
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;

/**
 * Class CRMServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class CRMServiceTest extends TestCase
{
    use HelperTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [
                CRMReadonlyContract::DRIVER_THERAPYLOG,
                [
                    'email' => $this->randString() . '@mail.com',
                    'password' => $this->randString()
                ],
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
        $this->userService__()->crmService()->workWith((string)$this->generateIdentity());
    }

    /**
     * @param string $driver
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
        array $config
    ): void {

        $crmService = $this->userService__()->crmService();
        $crmService->connect(
            [
                'driver' => $driver,
                'config' => $config,
            ]
        );

        self::assertEquals($driver, $crmService->readonly()->driver());

        $newConfig = collect($config)->map(function ($value, $key) {
            return [
                'type' => $key,
                'value' => $value,
                'encryption' => ($key === 'password'),
            ];
        })->values()->toArray();

        self::assertEquals((new Config($newConfig, $driver))->toArrayDecrypted(), $crmService->readonly()->config()->toArrayDecrypted());

        self::assertCount($crmService->listRO()->count(), $this->userService__()->readonly()->crms());
        self::assertCount($crmService->list()->count(), $this->userService__()->readonly()->crms());

        Event::fake();

        $crmService->change(['active' => false]);

        self::flush();

        self::assertFalse($crmService->readonly()->active());
        self::assertTrue($crmService->readonly()->notified());

        Event::assertDispatched(EventLost::class, function (EventLost $event) use ($crmService) {
            return $event->crm() === $crmService->readonly();
        });

        $dto = $crmService->dto();

        self::assertEquals($dto->driver, $crmService->readonly()->driver());
        self::assertEquals($dto->active, $crmService->readonly()->active());
        self::assertEquals($dto->notified, $crmService->readonly()->notified());

        $enabledErrorIsThrown = false;

        try {
            $crmService->disconnect();
        } catch (RuntimeException $exception) {
            $enabledErrorIsThrown = true;
        }

        self::assertTrue($enabledErrorIsThrown);

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

        $crmService = $this->userService__()->crmService();

        $crmService->connect(
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
