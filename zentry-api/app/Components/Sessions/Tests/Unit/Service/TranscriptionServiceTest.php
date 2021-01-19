<?php

namespace App\Components\Sessions\Tests\Unit\Service;

use App\Components\Sessions\Session\Transcription\TranscriptionDTO;
use App\Components\Sessions\Session\Transcription\TranscriptionReadonlyContract;
use App\Components\Sessions\Services\Transcription\Traits\TranscriptionServiceTrait;
use App\Components\Sessions\Session\Transcription\Mutators\DTO\Mutator;
use App\Components\Sessions\Tests\Unit\Traits\SessionHelperTestTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;
use UnexpectedValueException;

/**
 * Class TranscriptionServiceTest
 *
 * @package App\Components\Sessions\Tests\Unit\Service
 */
class TranscriptionServiceTest extends TestCase
{
    use TranscriptionServiceTrait;
    use AuthServiceTrait;
    use SessionHelperTestTrait;

    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws LockException
     * @throws MappingException
     * @throws LogicException
     */
    public function testWorkWithException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->transcriptionService__()->workWith((string)$this->generateIdentity());
    }

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
                $this->randString(8),
                0,
                1,
                null,
            ],
            [
                IdentityGenerator::next(),
                null,
                $this->randString(8),
                0.4,
                7.6,
                null,
            ],
            [
                IdentityGenerator::next(),
                IdentityGenerator::next(),
                $this->randString(8),
                5,
                4,
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * @dataProvider correctDataProvider
     *
     * @param Identity      $sessionIdentity
     * @param Identity|null $poiIdentity
     * @param string        $word
     * @param float         $startedAt
     * @param float         $endedAt
     * @param null|string   $exception
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws LockException
     * @throws LogicException
     * @throws MappingException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PHPUnitException
     * @throws PropertyNotInit
     * @throws RecursionContextInvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function testSuccessCreation(
        Identity $sessionIdentity,
        ?Identity $poiIdentity,
        string $word,
        float $startedAt,
        float $endedAt,
        ?string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $this->login();
        $userIdentity = $this->authService__()->user()->identity();
        $dto = $this->transcriptionService__()->create(
            [
                'user_id' => $userIdentity->toString(),
                'session_id' => $sessionIdentity->toString(),
                'poi_id' => $poiIdentity instanceof Identity ? $poiIdentity->toString() : null,
                'word' => $word,
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
            ]
        )->dto();

        self::assertInstanceOf(TranscriptionReadonlyContract::class, $this->transcriptionService__()->readonly());
        self::assertInstanceOf(TranscriptionDTO::class, $this->transcriptionService__()->dto());
        self::assertEquals(Mutator::TYPE, $dto->_type);

        self::assertEquals($word, $this->transcriptionService__()->readonly()->word());
        self::assertEquals($startedAt, $this->transcriptionService__()->readonly()->startedAt());
        self::assertEquals($endedAt, $this->transcriptionService__()->readonly()->endedAt());
        self::assertEquals($userIdentity, $this->transcriptionService__()->readonly()->userIdentity());
        self::assertEquals($sessionIdentity, $this->transcriptionService__()->readonly()->sessionIdentity());
        self::assertEquals($poiIdentity, $this->transcriptionService__()->readonly()->poiIdentity());

        self::assertCount(1, $this->transcriptionService__()->listRO());
        self::assertCount(1, $this->transcriptionService__()->list());

        $this->transcriptionService__()->workWith( $this->transcriptionService__()->readonly()->identity());
        $this->transcriptionService__()->remove();

        self::assertCount(0, $this->transcriptionService__()->listRO());
        self::assertCount(0, $this->transcriptionService__()->list());
    }
}
