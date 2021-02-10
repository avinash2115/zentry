<?php

namespace App\Components\Sessions\Tests\Unit\Repository;

use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\Repository\SessionRepositoryDoctrine;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Tests\Unit\Traits\SessionHelperTestTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContectInvalidArgumentException;
use Tests\TestCase;
use UnexpectedValueException;

/**
 * Class SessionRepositoryTest
 *
 * @package App\Components\Sessions\Tests\Unit\Repository
 */
class SessionRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use SessionHelperTestTrait;

    /**
     * @var SessionRepositoryDoctrine|null
     */
    private ?SessionRepositoryDoctrine $sessionRepository = null;

    /**
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     * @throws RecursionContectInvalidArgumentException
     */
    public function testPersist(): void
    {
        $this->refreshTestDatabase();

        $entity = $this->createFullSessionWithPois(true);
        $pois = $entity->pois();
        $this->sessionRepository()->persist($entity);
        $this->flush();
        $persistedEntity = $this->sessionRepository()->byIdentity($entity->identity());
        $persistedPois = $persistedEntity->pois();

        $pois->each(
            function (PoiReadonlyContract $poi) use ($persistedPois) {
                $persistedPoi = $persistedPois->get($poi->identity()->toString());

                if (!$persistedPoi instanceof PoiReadonlyContract) {
                    throw new UnexpectedValueException();
                }

                static::assertTrue($persistedPoi->identity()->equals($poi->identity()));
                static::assertEquals($persistedPoi->type(), $poi->type());
                static::assertEquals($persistedPoi->startedAt(), $poi->startedAt());
                static::assertEquals($persistedPoi->endedAt(), $poi->endedAt());
                static::assertEquals($persistedPoi->createdAt(), $poi->createdAt());
                static::assertEquals($persistedPoi->duration(), $poi->duration());
            }
        );

        static::assertEquals($persistedEntity->identity(), $entity->identity());
        static::assertEquals($persistedEntity->user(), $entity->user());

        static::assertCount(1, $this->sessionRepository()->getAll());
        $this->sessionRepository()->destroy($entity);
        $this->flush();
        static::assertCount(0, $this->sessionRepository()->getAll());
    }

    /**
     * Check by identity exception
     *
     * @return void
     * @throws BindingResolutionException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     * @throws RecursionContectInvalidArgumentException
     */
    public function testByIdentityException(): void
    {
        try {
            $this->sessionRepository()->byIdentity(IdentityGenerator::next());
            $this->assertTrue(false);
        } catch (NotFoundException $e) {
            $this->assertInstanceOf(NotFoundException::class, $e);
        }
    }

    /**
     * @return SessionRepositoryDoctrine
     * @throws BindingResolutionException
     */
    private function sessionRepository(): SessionRepositoryDoctrine
    {
        if (!$this->sessionRepository instanceof SessionRepositoryDoctrine) {
            $this->sessionRepository = app()->make(SessionRepositoryDoctrine::class);
        }

        return $this->sessionRepository;
    }
}