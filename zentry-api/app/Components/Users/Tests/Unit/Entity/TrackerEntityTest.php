<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\Participant\Goal\GoalContract;
use App\Components\Users\Participant\Goal\Tracker\TrackerContract;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\ValueObjects\Participant\Goal\Meta;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Class TrackerEntityTest
 *
 * @package App\Components\Users\Tests\Unit\Entity
 */
class TrackerEntityTest extends TestCase
{
    use HelperTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->randString(), $this->randString()],
            [$this->randString(), $this->randString()],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            ['', $this->randString()],
            [$this->randString(), ''],
        ];
    }

    /**
     * @param string $name
     * @param string $icon
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $name,
        string $icon
    ): void
    {
        $identity = $this->generateIdentity();
        $goal = $this->goal();

        $entity = $this->create($identity, $goal, $name, $icon);
        $goal->addTracker($entity);

        static::assertTrue($entity->identity()->equals($identity));

        static::assertEquals($name, $entity->name());
        static::assertEquals($icon, $entity->icon());


        $name = $this->randString();
        $icon = $this->randString();

        $entity->changeName($name);
        $entity->changeIcon($icon);

        static::assertEquals($name, $entity->name());
        static::assertEquals($icon, $entity->icon());

        static::assertCount(1, $goal->trackers());
        $tracker = $goal->trackerByIdentity($entity->identity());
        static::assertTrue($entity->identity()->equals($tracker->identity()));
        $goal->removeTracker($tracker);
        static::assertCount(0, $goal->trackers());
    }

    /**
     * @param string $name
     * @param string $icon
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $name,
        string $icon
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $goal = $this->goal();
        $this->create($this->generateIdentity(), $goal, $name, $icon);
    }

    /**
     * @param Identity     $identity
     * @param GoalContract $goal
     * @param string       $name
     * @param string       $icon
     *
     * @return TrackerContract
     * @throws BindingResolutionException
     */
    private function create(Identity $identity, GoalContract $goal, string $name, string $icon): TrackerContract
    {
        return $this->app->make(
            TrackerContract::class,
            [
                'identity' => $identity,
                'goal' => $goal,
                'name' => $name,
                'icon' => $icon,
            ]
        );
    }

    /**
     * @return GoalContract
     * @throws BindingResolutionException
     */
    private function goal(): GoalContract
    {
        return $this->app->make(
            GoalContract::class,
            [
                'identity' => $this->generateIdentity(),
                'participant' => $this->participant(),
                'name' => $this->randString(),
                'meta' => new Meta([]),
                'description' => $this->randString(),
            ]
        );
    }

    /**
     * @return ParticipantContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function participant(): ParticipantContract
    {
        return $this->app->make(
            ParticipantContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->createUser(),
                'team' => null,
                'email' => $this->randEmail(),
                'firstName' => $this->randString(),
                'lastName' => $this->randString(),
                'phoneCode' => null,
                'phoneNumber' => null,
                'avatar' => null,
                'gender' => null,
                'dob' => null,
                'school' => null,
            ]
        );
    }
}
