<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\Team\Mutators\DTO\Mutator as TeamMutator;
use App\Components\Users\Team\School\Mutators\DTO\Mutator as SchoolMutator;
use App\Convention\Tests\Traits\HelperTrait;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\IntegrationTestCase;

/**
 * Class SchoolIntegrationTest
 *
 * @package App\Components\Users\Tests\Unit\Integration
 */
class SchoolIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;

    /**
     * @throws NonUniqueResultException
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testCreate(): void
    {
        $response = $this->withAuthHeader()->json(
            'POST',
            route('users.teams.create'),
            $this->asData(
                TeamMutator::TYPE,
                [
                    'name' => $this->randString(),
                    'description' => $this->randString(),
                ]
            )
        );

        $response->assertStatus(201);

        $team = $this->asJsonApi($response);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.teams.schools.index', [$team->id()]),
        );

        $response->assertStatus(200);

        self::assertCount(0, $this->asJsonApi($response)->asJsonApiCollection());

        $response = $this->withAuthHeader()->json(
            'POST',
            route('users.teams.schools.create', [$team->id()]),
            $this->asData(
                SchoolMutator::TYPE,
                [
                    'name' => $this->randString(),
                ]
            )
        );

        $response->assertStatus(201);

        $school = $this->asJsonApi($response);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.teams.schools.show', [$team->id(), $school->id()]),
        );

        $response->assertStatus(200);
        self::assertEquals($this->asJsonApi($response)->id(), $school->id());

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.teams.schools.index', [$team->id()]),
        );

        $response->assertStatus(200);
        self::assertCount(1, $this->asJsonApi($response)->asJsonApiCollection());

        $name = $this->randString();
        $available = false;
        $streetAddress = $this->randString();
        $city = $this->randString();
        $state = $this->randString();
        $zip = $this->randString();

        $response = $this->withAuthHeader()->json(
            'POST',
            route('users.teams.schools.change', [$team->id(), $school->id()]),
            $this->asData(
                SchoolMutator::TYPE,
                [
                    'name' => $name,
                    'available' => $available,
                    'street_address' => $streetAddress,
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                ]
            )
        );

        $school = $this->asJsonApi($response);

        $response->assertStatus(200);

        self::assertEquals($name, $school->attributes()->get('name'));
        self::assertEquals($available, $school->attributes()->get('available'));
        self::assertEquals($streetAddress, $school->attributes()->get('street_address'));
        self::assertEquals($city, $school->attributes()->get('city'));
        self::assertEquals($state, $school->attributes()->get('state'));
        self::assertEquals($zip, $school->attributes()->get('zip'));

        $this->withAuthHeader()->json(
            'POST',
            route('users.teams.schools.create', [$team->id()]),
            $this->asData(SchoolMutator::TYPE, [
            ])
        )->assertStatus(500);

        $this->withAuthHeader()->json(
            'POST',
            route('users.teams.schools.create', [$team->id()]),
            $this->asData(SchoolMutator::TYPE, [
                'name' => $this->randString(),
                'available' => $this->randString(),
            ])
        )->assertStatus(500);

        $response = $this->withAuthHeader()->withDeleteHeader()->json(
            'POST',
            route('users.teams.schools.remove', [$team->id(), $school->id()]),
            $this->asData(SchoolMutator::TYPE)
        );

        $response->assertStatus(200)->assertJson(['acknowledge' => true]);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.teams.schools.index', [$team->id()]),
        );

        $response->assertStatus(200);
        self::assertCount(0, $this->asJsonApi($response)->asJsonApiCollection());
    }
}
