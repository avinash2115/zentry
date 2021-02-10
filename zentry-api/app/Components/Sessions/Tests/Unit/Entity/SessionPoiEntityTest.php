<?php

namespace App\Components\Sessions\Tests\Unit\Entity;

use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Tests\Unit\Traits\SessionHelperTestTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Tags;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContectInvalidArgumentException;
use Tests\TestCase;

/**
 * Class SessionPoiEntityTest
 */
class SessionPoiEntityTest extends TestCase
{
    use SessionHelperTestTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            [
                PoiReadonlyContract::POI_TYPE,
                new DateTime(),
                (new DateTime())->add(new DateInterval('PT5S')),
                RuntimeException::class,
            ],
            [
                PoiReadonlyContract::POI_TYPE,
                (new DateTime())->add(new DateInterval('PT5S')),
                new DateTime(),
                RuntimeException::class,
            ],
            [
                $this->randString(),
                new DateTime(),
                (new DateTime())->add(new DateInterval('PT15S')),
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * @param string   $type
     * @param DateTime $startedAt
     * @param DateTime $endedAt
     * @param string   $error
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(string $type, DateTime $startedAt, DateTime $endedAt, string $error): void
    {
        $this->expectException($error);

        $session = $this->createSession(
            IdentityGenerator::next(),
            $this->randString(),
            new Tags([]),
            $this->createUser()
        );

        $this->createPoi($this->generateIdentity(), $type, $startedAt, $endedAt, $session);
    }

    /**
     * @param string      $type
     * @param DateTime    $startedAt
     * @param DateTime    $endedAt
     * @param string|null $name
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws RecursionContectInvalidArgumentException
     * @dataProvider correctPoiDataProvider
     */
    public function testCreateCorrect(
        string $type,
        DateTime $startedAt,
        DateTime $endedAt,
        ?string $name = null
    ): void {
        $identity = $this->generateIdentity();
        $session = $this->createSession(
            IdentityGenerator::next(),
            $this->randString(),
            new Tags([]),
            $this->createUser()
        );

        $entity = $this->createPoi($identity, $type, $startedAt, $endedAt, $session, [], $name);

        static::assertTrue($entity->identity()->equals($identity));
        static::assertEquals($entity->type(), $type);
        static::assertEquals($entity->name(), $name);
        static::assertEquals($entity->startedAt(), $startedAt);
        static::assertEquals($entity->endedAt(), $endedAt);

        $newName = $this->randString();

        $entity->changeName($newName);
        static::assertEquals($entity->name(), $newName);
        static::assertNotEquals($entity->name(), $name);

        $entity->changeName();
        static::assertEquals($entity->name(), null);

        static::assertNotNull($entity->createdAt());
    }
}
