<?php

namespace App\Components\Sessions\Tests\Unit\Entity;

use App\Components\Sessions\Session\Note\NoteContract;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Tests\Unit\Traits\SessionHelperTestTrait;
use App\Components\Sessions\ValueObjects\Note\Payload;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContectInvalidArgumentException;
use Tests\TestCase;

/**
 * Class NoteEntityTest
 */
class NoteEntityTest extends TestCase
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
                $this->randString(),
                null,
            ],
            [
                $this->randString(),
                $this->randString(),
            ],
            [
                null,
                $this->randString(),
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
                null,
                null,
            ],
        ];
    }

    /**
     * @param string|null $text
     * @param string|null $url
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(?string $text, ?string $url): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->create(
            IdentityGenerator::next(), new Payload($text, $url), $this->createFullSessionWithPois(),
        );
    }

    /**
     * @param string|null $text
     * @param string|null $url
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws RecursionContectInvalidArgumentException
     * @throws RuntimeException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        ?string $text, ?string $url
    ): void
    {
        $identity = $this->generateIdentity();
        $session = $this->createFullSessionWithPois();

        $payload = new Payload($text, $url);

        $entity = $this->create($identity, $payload, $session);

        $session->addNote($entity);

        static::assertCount(1, $session->notes());

        static::assertTrue($entity->identity()->equals($identity));
        if ($text === null) {
            static::assertEquals($entity->text(), '');
        } else {
            static::assertEquals($entity->text(), $text);
        }

        static::assertEquals($entity->url(), $url);
        static::assertEquals($entity->participant(), $payload->participant());
        static::assertEquals($entity->poi(), $payload->poi());
        static::assertEquals($entity->poiParticipant(), $payload->poiParticipant());
        static::assertEquals($entity->url(), $url);

        static::assertNotNull($entity->createdAt());
        static::assertNotNull($entity->updatedAt());

        $poi = $session->pois()->first();
        static::assertInstanceOf(PoiReadonlyContract::class, $poi);

        $participant = $this->participant();

        $note = $this->create(IdentityGenerator::next(), new Payload($text, $url, $participant, $poi), $session);

        static::assertEquals($poi, $note->poi());
        static::assertEquals($participant, $note->participant());
    }

    /**
     * @param Identity        $identity
     * @param Payload         $payload
     * @param SessionContract $session
     *
     * @return NoteContract
     * @throws BindingResolutionException
     */
    private function create(Identity $identity, Payload $payload, SessionContract $session): NoteContract
    {
        return app()->make(NoteContract::class, [
            'identity' => $identity,
            'session' => $session,
            'payload' => $payload,
        ]);
    }
}
