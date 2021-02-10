<?php

namespace App\Components\Users\Tests\Unit\Repository;

use App\Components\Users\Participant\Goal\GoalContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Tracker\TrackerContract;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Participant\Repository\ParticipantRepositoryDoctrine;
use App\Components\Users\Services\Participant\Goal\Tracker\TrackerServiceContract;
use App\Components\Users\User\Repository\UserRepositoryDoctrine;
use App\Components\Users\ValueObjects\Participant\Goal\Meta;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Tests\TestCase;

/**
 * Class ParticipantRepositoryTest
 *
 * @package App\Components\Users\Tests\Unit\Repository
 */
class ParticipantRepositoryTest extends TestCase
{
    use HelperTrait;
    use RefreshDatabase;

    /**
     * @var ParticipantRepositoryDoctrine|null
     */
    private ?ParticipantRepositoryDoctrine $repository = null;

    /**
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testPersist(): void
    {
        $participant = $this->createParticipant();
        $this->addGoals($participant);
        $this->repository()->persist($participant);

        $this->flush();

        $persistedParticipant = $this->repository()->byIdentity($participant->identity());

        static::assertEquals($persistedParticipant->identity(), $participant->identity());
        static::assertEquals($persistedParticipant->email(), $participant->email());
        static::assertEquals($persistedParticipant->firstName(), $participant->firstName());
        static::assertEquals($persistedParticipant->lastName(), $participant->lastName());
        static::assertEquals($persistedParticipant->phoneNumber(), $participant->phoneNumber());
        static::assertEquals($persistedParticipant->phoneCode(), $participant->phoneCode());

        $participant->goals()->each(
            static function (GoalReadonlyContract $goal) use ($persistedParticipant) {
                $persistedGoal = $persistedParticipant->goals()->first(
                    function (GoalReadonlyContract $persistedGoal) use ($goal) {
                        return $persistedGoal->identity()->equals($goal->identity());
                    }
                );

                if (!$persistedGoal instanceof GoalReadonlyContract) {
                    throw new RuntimeException();
                }

                self::assertInstanceOf(GoalReadonlyContract::class, $persistedGoal);
                static::assertTrue($goal->identity()->equals($persistedGoal->identity()));
                static::assertEquals($goal->name(), $persistedGoal->name());
                static::assertEquals($goal->description(), $persistedGoal->description());
                static::assertEquals($goal->isReached(), $persistedGoal->isReached());
                static::assertEquals($goal->createdAt(), $persistedGoal->createdAt());
                static::assertEquals($goal->updatedAt(), $persistedGoal->updatedAt());

                $goal->trackers()->each(function(TrackerContract $tracker) use ($persistedGoal) {
                    $persistedTracker = $persistedGoal->trackers()->first(
                        function (TrackerContract $persistedTracker) use ($tracker) {
                            return $persistedTracker->identity()->equals($tracker->identity());
                        }
                    );

                    if (!$persistedTracker instanceof TrackerContract) {
                        throw new RuntimeException();
                    }

                    self::assertInstanceOf(TrackerContract::class, $persistedTracker);
                    static::assertTrue($tracker->identity()->equals($persistedTracker->identity()));
                    static::assertEquals($tracker->name(), $persistedTracker->name());
                    static::assertEquals($tracker->icon(), $persistedTracker->icon());
                    static::assertEquals($tracker->createdAt(), $persistedTracker->createdAt());
                });
            }
        );

        static::assertCount(1, $this->repository()->getAll());

        $goal = $participant->goals()->first();

        if (!$goal instanceof GoalReadonlyContract) {
            throw new RuntimeException();
        }

        static::assertCount(1, $this->repository()->filterByGoalsIds([$goal->identity()])->getAll());
        static::assertCount(1, $this->repository()->filterByUserIds([$participant->user()->identity()])->getAll());
        static::assertCount(0, $this->repository()->filterByGoalsIds([$this->generateIdentity()])->getAll());

        $this->repository()->destroy($participant);
        $this->flush();

        static::assertCount(0, $this->repository()->getAll());
    }

    /**
     * Check by identity exception
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testByIdentityException(): void
    {
        try {
            $this->repository()->byIdentity(IdentityGenerator::next());
            static::assertTrue(false);
        } catch (NotFoundException $e) {
            static::assertInstanceOf(NotFoundException::class, $e);
        }
    }

    /**
     * @param ParticipantContract $participant
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function addGoals(ParticipantContract $participant): void
    {
        for ($i = 0; $i < random_int(1, 10); $i++) {
            $goal = app()->make(
                GoalContract::class,
                [
                    'identity' => IdentityGenerator::next(),
                    'participant' => $participant,
                    'name' => $this->randString(),
                    'description' => $this->randString(),
                    'meta' => new Meta([]),
                ]
            );
            $participant->addGoal($goal);

            $this->addTrackers($goal);
        }
    }

    /**
     * @param GoalContract $goal
     */
    private function addTrackers(GoalContract $goal): void
    {
        collect(TrackerServiceContract::DEFAULT_TRACKERS)->each(function(string $icon, string $name) use ($goal) {
            $goal->addTracker(app()->make(TrackerContract::class, [
                'identity' => $this->generateIdentity(),
                'goal' => $goal,
                'name' => $name,
                'icon' => $icon,
            ]));
        });
    }
    /**
     * @return ParticipantRepositoryDoctrine
     * @throws BindingResolutionException
     */
    private function repository(): ParticipantRepositoryDoctrine
    {
        if (!$this->repository instanceof ParticipantRepositoryDoctrine) {
            $this->repository = app()->make(ParticipantRepositoryDoctrine::class);
        }

        return $this->repository;
    }

    /**
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function createParticipant(): ParticipantContract
    {
        return $this->app->make(
            ParticipantContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->app->make(UserRepositoryDoctrine::class)->persist($this->createUser()),
                'team' => null,
                'school' => null,
                'email' => $this->randEmail(),
                'firstName' => $this->randString(),
                'lastName' => $this->randString(),
                'phoneCode' => null,
                'phoneNumber' => null,
                'avatar' => null,
            ]
        );
    }
}
