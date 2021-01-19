<?php

namespace App\Components\Sessions\Tests\Unit\Repository;

use App\Components\Sessions\Session\Transcription\Repository\TranscriptionRepositoryODM;
use App\Components\Sessions\Session\Transcription\TranscriptionContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContectInvalidArgumentException;
use Tests\TestCase;
use UnexpectedValueException;

/**
 * Class TranscriptionRepositoryTest
 *
 * @package App\Components\Sessions\Tests\Unit\Repository
 */
class TranscriptionRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use HelperTrait;

    /**
     * @var TranscriptionRepositoryODM|null
     */
    private ?TranscriptionRepositoryODM $repository = null;

    /**
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws LockException
     * @throws LogicException
     * @throws MappingException
     * @throws NotFoundException
     * @throws PHPUnitException
     * @throws RecursionContectInvalidArgumentException
     * @throws UnexpectedValueException
     * @throws MongoDBException
     */
    public function testPersist(): void
    {
        $entity = $this->createTranscription();
        $this->repository()->persist($entity);
        $this->flush();
        $persistedEntity = $this->repository()->byIdentity($entity->identity());

        static::assertEquals($persistedEntity->identity(), $entity->identity());
        static::assertEquals($persistedEntity->userIdentity(), $entity->userIdentity());
        static::assertEquals($persistedEntity->poiIdentity(), $entity->poiIdentity());
        static::assertEquals($persistedEntity->sessionIdentity(), $entity->sessionIdentity());
        static::assertEquals($persistedEntity->word(), $entity->word());
        static::assertEquals($persistedEntity->startedAt(), $entity->startedAt());
        static::assertEquals($persistedEntity->endedAt(), $entity->endedAt());

        static::assertCount(1, $this->repository()->getAll());
        $this->repository()->destroy($entity);
        $this->flush();
        static::assertCount(0, $this->repository()->getAll());
    }

    /**
     * Check by identity exception
     *
     * @return void
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws PHPUnitException
     * @throws RecursionContectInvalidArgumentException
     * @throws UnexpectedValueException
     * @throws LockException
     * @throws MappingException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function testByIdentityException(): void
    {
        try {
            $this->repository()->byIdentity(IdentityGenerator::next());
            self::assertTrue(false);
        } catch (NotFoundException $e) {
            self::assertInstanceOf(NotFoundException::class, $e);
        }
    }

    /**
     * @return TranscriptionRepositoryODM
     * @throws BindingResolutionException
     */
    private function repository(): TranscriptionRepositoryODM
    {
        if (!$this->repository instanceof TranscriptionRepositoryODM) {
            $this->repository = app()->make(TranscriptionRepositoryODM::class);
        }

        return $this->repository;
    }

    /**
     * @return TranscriptionContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function createTranscription(): TranscriptionContract
    {
        return $this->app->make(
            TranscriptionContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'userIdentity' => IdentityGenerator::next(),
                'sessionIdentity' => IdentityGenerator::next(),
                'poiIdentity' => IdentityGenerator::next(),
                'word' => $this->randString(),
                'startedAt' => random_int(0, 5),
                'endedAt' => random_int(6, 10),
            ]
        );
    }

    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws MongoDBException
     * @throws UnexpectedValueException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository()->getAll()->each(
            function (TranscriptionContract $transcription) {
                $this->repository()->destroy($transcription);
            }
        );
    }
}
