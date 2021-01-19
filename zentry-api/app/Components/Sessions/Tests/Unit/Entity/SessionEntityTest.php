<?php

namespace App\Components\Sessions\Tests\Unit\Entity;

use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Tests\Unit\Traits\SessionHelperTestTrait;
use App\Components\Sessions\ValueObjects\Geo;
use App\Convention\ValueObjects\Tags;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContectInvalidArgumentException;
use Tests\TestCase;

/**
 * Class SessionEntityTest
 */
class SessionEntityTest extends TestCase
{
    use SessionHelperTestTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [
                $this->randString(8),
                SessionReadonlyContract::TYPE_DEFAULT,
                $this->randString(),
                new Tags([]),
                $this->randString(),
                null,
                null,
            ],
            [
                $this->randString(5),
                SessionReadonlyContract::TYPE_DEFAULT,
                $this->randString(),
                new Tags([]),
                null,
                null,
            ],
            [
                $this->randString(5),
                SessionReadonlyContract::TYPE_DEFAULT,
                $this->randString(),
                new Tags([]),
                $this->randString(),
                null,
                null,
            ],
            [
                $this->randString(5),
                SessionReadonlyContract::TYPE_DEFAULT,
                $this->randString(),
                new Tags([]),
                null,
                null,
                null,
            ],
            [
                $this->randString(8),
                SessionReadonlyContract::TYPE_DEFAULT,
                $this->randString(),
                new Tags([]),
                $this->randString(),
                new DateTime(),
                new DateTime(),
            ],
            [
                $this->randString(8),
                SessionReadonlyContract::TYPE_DEFAULT,
                $this->randString(),
                new Tags([]),
                null,
                null,
                new DateTime(),
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
            [
                InvalidArgumentException::class,
                '',
                SessionReadonlyContract::TYPE_EVAL_OF_LANG_COMPREHENSION_AND_EXPRESSION_ONLY,
                $this->randString(),
                new Tags([]),
                $this->randString(),
                null,
                null,
            ],
            [
                InvalidArgumentException::class,
                $this->randString(5),
                $this->randString(),
                $this->randString(),
                new Tags([]),
                $this->randString(),
                null,
                null,
            ],
            [
                InvalidArgumentException::class,
                $this->randString(5),
                SessionReadonlyContract::TYPE_DEFAULT,
                $this->randString(),
                new Tags([]),
                null,
                new DateTime(),
                null,
            ],
        ];
    }

    /**
     * @param string        $error
     * @param string        $name
     * @param string        $type
     * @param string        $description
     * @param Tags          $tags
     * @param string|null   $reference
     * @param DateTime|null $scheduledOn
     * @param DateTime|null $scheduledTo
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $error,
        string $name,
        string $type,
        string $description,
        Tags $tags,
        string $reference = null,
        ?DateTime $scheduledOn = null,
        ?DateTime $scheduledTo = null
    ): void {
        $this->expectException($error);
        $user = $this->createUser();

        $this->createSession(
            $this->generateIdentity(),
            $name,
            $tags,
            $user,
            $type,
            $description,
            $reference,
            $scheduledOn,
            $scheduledTo
        );
    }

    /**
     * @param string        $name
     * @param string        $type
     * @param string        $description
     * @param Tags          $tags
     * @param string|null   $reference
     * @param DateTime|null $scheduledOn
     * @param DateTime|null $scheduledTo
     *
     * @return SessionContract
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws RecursionContectInvalidArgumentException
     * @throws RuntimeException
     * @throws \PHPUnit\Framework\Exception
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $name,
        string $type,
        string $description,
        Tags $tags,
        string $reference = null,
        ?DateTime $scheduledOn = null,
        ?DateTime $scheduledTo = null
    ): SessionContract {
        $identity = $this->generateIdentity();
        $user = $this->createUser();

        $entity = $this->createSession(
            $identity,
            $name,
            $tags,
            $user,
            $type,
            $description,
            $reference,
            $scheduledOn,
            $scheduledTo
        );

        static::assertNull($entity->startedAt());

        $entity->start();
        static::assertTrue($entity->isStarted());

        static::assertNotNull($entity->startedAt());

        static::assertTrue($entity->identity()->equals($identity));
        static::assertEquals($entity->name(), $name);

        static::assertNotNull($entity->createdAt());
        static::assertNull($entity->endedAt());

        $entity->end();
        static::assertTrue($entity->isEnded());
        static::assertNotNull($entity->endedAt());

        static::assertContains($entity->type(), SessionReadonlyContract::AVAILABLE_TYPES);
        self::assertEquals($type, $entity->type());

        self::assertEquals($name, $entity->name());
        self::assertEquals($description, $entity->description());

        if ($scheduledOn === null && $scheduledTo instanceof DateTime) {
            self::assertNull($entity->scheduledTo());
        }

        $entity->wrap();
        static::assertTrue($entity->isWrapped());

        return $entity;
    }

    /**
     * @param SessionContract $session
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws RecursionContectInvalidArgumentException
     */
    private function checkChanges(SessionContract $session): void
    {
        $geo = new Geo(45.707198, 34.761019, $this->randString());

        $tags = new Tags(
            [
                [
                    'tag' => $this->randString(),
                ],
                [
                    'tag' => $this->randString(),
                ],
            ]
        );

        $name = $this->randString();

        $session->changeName($name);
        $session->changeGeo($geo);
        $session->changeTags($tags);

        self::assertTrue($geo->equals($session->geo()));
        self::assertEquals($tags, $session->tags());
        self::assertEquals($name, $session->name());

        $session->changeTags(new Tags([]));
        $session->changeGeo(null);

        self::assertEquals(null, $session->geo());
        self::assertEquals(new Tags([]), $session->tags());
    }
}
