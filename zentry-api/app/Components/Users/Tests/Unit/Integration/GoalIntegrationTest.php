<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\Participant\Mutators\DTO\Mutator as ParticipantMutator;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Convention\Tests\Traits\HelperTrait;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\IntegrationTestCase;

/**
 * Class GoalIntegrationTest
 */
class GoalIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;
    use DeviceServiceTrait;
    use AuthServiceTrait;

    /**
     * @throws NonUniqueResultException
     * @throws BindingResolutionException
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

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.participants.goals.index', [$participant->id()]),
        );

        $response->assertStatus(200);

        self::assertCount(0, $this->asJsonApi($response)->asJsonApiCollection());

        $response = $this->withAuthHeader()->json(
            'POST',
            route('users.participants.goals.create', [$participant->id()]),
            $this->asData(\App\Components\Users\Participant\Goal\Mutators\DTO\Mutator::TYPE, [
                'name' => $this->randString(),
            ])
        );

        $goal = $this->asJsonApi($response);

        $response->assertStatus(201);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.participants.goals.index', [$participant->id()]),
        );

        $response->assertStatus(200);
        self::assertCount(1, $this->asJsonApi($response)->asJsonApiCollection());

        $description = $this->randString();

        $response = $this->withAuthHeader()->json(
            'POST',
            route('users.participants.goals.change', [$participant->id(), $goal->id()]),
            $this->asData(\App\Components\Users\Participant\Goal\Mutators\DTO\Mutator::TYPE, [
                'description' => $description,
            ])
        );

        $goal = $this->asJsonApi($response);

        $response->assertStatus(200);

        self::assertEquals($description, $goal->attributes()->get('description'));

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.participants.goals.show', [$participant->id(), $goal->id()]),
        );

        $response->assertStatus(200);
        self::assertEquals($this->asJsonApi($response)->id(), $goal->id());

        $this->withAuthHeader()->json(
            'POST',
            route('users.participants.goals.create', [$participant->id()]),
            $this->asData(\App\Components\Users\Participant\Goal\Mutators\DTO\Mutator::TYPE, [
            ])
        )->assertStatus(500);

        $response = $this->withAuthHeader()->withDeleteHeader()->json(
            'POST',
            route('users.participants.goals.remove', [$participant->id(), $goal->id()]),
            $this->asData(\App\Components\Users\Participant\Goal\Mutators\DTO\Mutator::TYPE)
        );

        $response->assertStatus(200)->assertJson(['acknowledge' => true]);
    }
}
