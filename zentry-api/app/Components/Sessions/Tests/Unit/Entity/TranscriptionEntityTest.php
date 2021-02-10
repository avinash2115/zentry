<?php

namespace App\Components\Sessions\Tests\Unit\Entity;

use App\Components\Sessions\Session\Transcription\TranscriptionContract;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContectInvalidArgumentException;
use Tests\TestCase;

/**
 * Class TranscriptionEntityTest
 */
class TranscriptionEntityTest extends TestCase
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
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                $this->randString(8),
                0,
                1
            ],
            [
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                null,
                $this->randString(8),
                1.5,
                5.9
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
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                $this->randString(8),
                1,
                0,
                InvalidArgumentException::class
            ],
            [
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                $this->randString(8),
                5,
                4,
                InvalidArgumentException::class
            ],
            [
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                '',
                1.5,
                5.9,
                InvalidArgumentException::class
            ],
        ];
    }

    /**
     * @param Identity      $userIdentity
     * @param Identity      $sessionIdentity
     * @param Identity|null $poiIdentity
     * @param string        $word
     * @param float         $startedAt
     * @param float         $endedAt
     * @param string        $error
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        Identity $userIdentity,
        Identity $sessionIdentity,
        ?Identity $poiIdentity,
        string $word,
        float $startedAt,
        float $endedAt,
        string $error
    ): void {
        $this->expectException($error);
        $this->createTranscription(IdentityGenerator::next(), $userIdentity, $sessionIdentity, $poiIdentity, $word, $startedAt, $endedAt);
    }

    /**
     * @param Identity      $userIdentity
     * @param Identity      $sessionIdentity
     * @param Identity|null $poiIdentity
     * @param string        $word
     * @param float         $startedAt
     * @param float         $endedAt
     *
     * @return TranscriptionContract
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws RecursionContectInvalidArgumentException
     * @throws RuntimeException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        Identity $userIdentity,
        Identity $sessionIdentity,
        ?Identity $poiIdentity,
        string $word,
        float $startedAt,
        float $endedAt
    ): TranscriptionContract {
        $identity = $this->generateIdentity();
        $entity = $this->createTranscription($identity, $userIdentity, $sessionIdentity, $poiIdentity, $word, $startedAt, $endedAt);

        static::assertTrue($entity->identity()->equals($identity));
        static::assertTrue($entity->sessionIdentity()->equals($sessionIdentity));
        static::assertTrue($entity->userIdentity()->equals($userIdentity));
        static::assertEquals($entity->poiIdentity(), $poiIdentity);

        static::assertEquals($entity->word(), $word);
        static::assertEquals($entity->startedAt(), $startedAt);
        static::assertEquals($entity->endedAt(), $endedAt);

        static::assertLessThan($endedAt, $startedAt);

        static::assertNotNull($entity->createdAt());
        static::assertNotNull($entity->startedAt());
        static::assertNotNull($entity->endedAt());

        return $entity;
    }

    /**
     * @param Identity      $id
     * @param Identity      $userIdentity
     * @param Identity      $sessionIdentity
     * @param Identity|null $poiIdentity
     * @param string        $word
     * @param float         $startedAt
     * @param float         $endedAt
     *
     * @return TranscriptionContract
     * @throws BindingResolutionException
     */
    private function createTranscription(
        Identity $id,
        Identity $userIdentity,
        Identity $sessionIdentity,
        ?Identity $poiIdentity,
        string $word,
        float $startedAt,
        float $endedAt
    ): TranscriptionContract {
        return $this->app->make(
            TranscriptionContract::class,
            [
                'identity' => $id,
                'userIdentity' => $userIdentity,
                'sessionIdentity' => $sessionIdentity,
                'poiIdentity' => $poiIdentity,
                'word' => $word,
                'startedAt' => $startedAt,
                'endedAt' => $endedAt,
            ]
        );
    }
}
