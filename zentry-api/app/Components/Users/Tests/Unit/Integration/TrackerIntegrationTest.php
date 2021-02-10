<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Components\Users\Participant\Mutators\DTO\Mutator as ParticipantMutator;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\Services\Participant\Goal\Tracker\TrackerServiceContract;
use App\Convention\Tests\Traits\HelperTrait;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\IntegrationTestCase;

/**
 * Class TrackerIntegrationTest
 */
class TrackerIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;
    use DeviceServiceTrait;
    use AuthServiceTrait;

    /**
     * @throws NonUniqueResultException
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testCreate(): void
    {
        $response = $this->withAuthHeader()->json(
            'POST',
            '/participants',
            $this->asData(
                ParticipantMutator::TYPE,
                [
                    'email' => $this->randEmail(),
                    'firstName' => $this->randString(),
                    'lastName' => $this->randString(),
                ]
            )
        );

        $participant = $this->asJsonApi($response);

        $goal = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                route('users.participants.goals.create', [$participant->id()]),
                $this->asData(
                    \App\Components\Users\Participant\Goal\Mutators\DTO\Mutator::TYPE,
                    [
                        'name' => $this->randString(),
                    ]
                )
            )
        );

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.participants.goals.trackers.index', [$participant->id(), $goal->id()])
        );

        $response->assertStatus(200);

        self::assertCount(
            count(TrackerServiceContract::DEFAULT_TRACKERS),
            $this->asJsonApi($response)->asJsonApiCollection()
        );

        $tracker = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                route('users.participants.goals.trackers.create', [$participant->id(), $goal->id()]),
                $this->asData(
                    \App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator::TYPE,
                    [
                        'name' => $this->randString(),
                        'icon' => $this->randString(),
                    ]
                )
            )->assertStatus(201)
        );

        $name = $this->randString();
        $type = TrackerReadonlyContract::TYPE_NEUTRAL;
        $icon = $this->randString();
        $color = $this->randString();

        $tracker = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                route('users.participants.goals.trackers.change', [$participant->id(), $goal->id(), $tracker->id()]),
                $this->asData(
                    \App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator::TYPE,
                    [
                        'name' => $name,
                        'type' => $type,
                        'icon' => $icon,
                        'color' => $color,
                    ]
                )
            )->assertStatus(200)
        );

        self::assertEquals($name, $tracker->attributes()->get('name'));
        self::assertEquals($icon, $tracker->attributes()->get('icon'));

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.participants.goals.trackers.show', [$participant->id(), $goal->id(), $tracker->id()]),
            );

        $response->assertStatus(200);

        self::assertEquals($this->asJsonApi($response)->id(), $tracker->id());

        $this->withAuthHeader()->json(
            'POST',
            route('users.participants.goals.trackers.create', [$participant->id(), $goal->id()]),
            $this->asData(
                \App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator::TYPE,
                []
            )
        )->assertStatus(500);

        $response = $this->withAuthHeader()->withDeleteHeader()->json(
            'POST',
            route('users.participants.goals.trackers.remove', [$participant->id(), $goal->id(), $tracker->id()]),
            $this->asData(\App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator::TYPE)
        );

        $response->assertStatus(200)->assertJson(['acknowledge' => true]);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.participants.goals.trackers.index', [$participant->id(), $goal->id()])
        );

        $response->assertStatus(200);
        self::assertCount(
            count(TrackerServiceContract::DEFAULT_TRACKERS),
            $this->asJsonApi($response)->asJsonApiCollection()
        );

        $goal = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                route('users.participants.goals.create', [$participant->id()]),
                $this->asData(
                    \App\Components\Users\Participant\Goal\Mutators\DTO\Mutator::TYPE,
                    [
                        'name' => $this->randString(),
                    ],
                    [
                        'trackers' => [
                            "data" => [
                                [
                                    "attributes" => [
                                        'name' => $this->randString(),
                                        'type' => TrackerReadonlyContract::TYPE_NEUTRAL,
                                        'icon' => $this->randString(),
                                        'color' => $this->randString(),
                                    ],
                                ],
                                [
                                    "attributes" => [
                                        'name' => $this->randString(),
                                        'type' => TrackerReadonlyContract::TYPE_NEUTRAL,
                                        'icon' => $this->randString(),
                                        'color' => $this->randString(),
                                    ],
                                ],
                            ],
                        ],
                    ]
                )
            )
        );

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.participants.goals.trackers.index', [$participant->id(), $goal->id()])
        );

        $response->assertStatus(200);

        self::assertCount(2, $this->asJsonApi($response)->asJsonApiCollection());
    }
}
