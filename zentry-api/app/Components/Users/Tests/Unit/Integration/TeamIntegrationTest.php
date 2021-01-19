<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\Team\Mutators\DTO\Mutator as TeamMutator;
use App\Components\Users\User\Mutators\DTO\Mutator;
use App\Components\Users\User\Profile\Mutators\DTO\Mutator as ProfileMutator;
use App\Convention\Tests\Traits\HelperTrait;
use Exception;
use Illuminate\Support\Arr;
use Tests\IntegrationTestCase;

/**
 * Class TeamIntegrationTest
 */
class TeamIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;
    use DeviceServiceTrait;
    use AuthServiceTrait;

    /**
     * @throws Exception
     */
    public function testFullFlow(): void
    {
        $response = $this->withAuthHeader()->json(
            'GET',
            '/teams'
        );

        $response->assertStatus(200);
        $response->assertJson([]);

        $this->withAuthHeader()->json(
            'POST',
            '/teams',
            $this->asData(
                TeamMutator::TYPE,
                [
                    'name' => '',
                    'description' => $this->randString(),
                ]
            )
        )->assertStatus(500);

        $response = $this->withAuthHeader()->json(
            'POST',
            '/teams',
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

        $password = $this->randString();

        $response = $this->json(
            'POST',
            '/auth/signup',
            $this->asData(
                Mutator::TYPE,
                [
                    'email' => $this->randString() . '@zentry.test',
                    'password' => $password,
                    'password_repeat' => $password,
                ],
                [
                    'profile' => $this->asData(
                        ProfileMutator::TYPE,
                        [
                            'first_name' => $this->randString(),
                            'last_name' => $this->randString(),
                        ]
                    ),
                ]
            )
        );

        $requestUserToken = Arr::get($this->asJsonApi($response)->attributes()->toArray(), 'token');

        $user = $this->asJsonApi(
            $this->withHeader(
                'Authorization',
                'Bearer ' . $requestUserToken,
                )->json(
                'GET',
                'users/current',
                )->assertStatus(200)
        );

        $this->withAuthHeader()->json(
            'GET',
            route('users.teams.show', [$team->id()]),
            )->assertStatus(200);

        $this->withAuthHeader()->json(
            'POST',
            route('users.teams.requests.create', [$team->id()]),
            [
                'data' => [
                    'attributes' => [
                        'link' => 'http://zentry.test/teams',
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => $user->id(),
                            ],
                        ],
                    ],
                ],
            ],
            )->assertStatus(500);
        $response = $this->withAuthHeader()->json(
            'POST',
            route('users.teams.requests.create', [$team->id()]),
            [
                'data' => [
                    'attributes' => [
                        'link' => 'http://zentry.test/teams/{id}',
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => $user->id(),
                            ],
                        ],
                    ],
                ],
            ],
            );
        $response->assertStatus(201);

        $request = $this->asJsonApi($response);

        $this->withHeader(
            'Authorization',
            'Bearer ' . $requestUserToken,
            )->json(
            'POST',
            route('users.teams.requests.apply', [$team->id(), $request->id()]),
            $this->asData('')
        )->assertStatus(200);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.teams.show', [$team->id()]) . '?include=*',
            );
        $team = $this->asJsonApi($response);
        self::assertCount(2, $team->relations('members'));

        $this->withHeaders(
            [
                'X-HTTP-METHOD-OVERRIDE' => 'DELETE',
                'Authorization' => 'Bearer ' . $requestUserToken,
            ]
        )->json(
            'POST',
            route('users.teams.leave', [$team->id()]),
            $this->asData('')
        );

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.teams.show', [$team->id()]) . '?include=*',
            );
        $team = $this->asJsonApi($response);
        self::assertCount(1, $team->relations('members'));

        $response = $this->withAuthHeader()->json(
            'POST',
            route('users.teams.requests.create', [$team->id()]),
            [
                'data' => [
                    'attributes' => [
                        'link' => 'http://zentry.test/teams/{id}',
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => $user->id(),
                            ],
                        ],
                    ],
                ],
            ],
            );
        $response->assertStatus(201);

        $request = $this->asJsonApi($response);

        $this->withHeader(
            'Authorization',
            'Bearer ' . $requestUserToken,
            )->json(
            'POST',
            route('users.teams.requests.reject', [$team->id(), $request->id()]),
            $this->asData('')
        )->assertStatus(200);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('users.teams.show', [$team->id()]) . '?include=*',
            );
        $team = $this->asJsonApi($response);

        self::assertCount(1, $team->relations('members'));
        self::assertEmpty($team->relations('requests'));

        $this->withAuthHeader()->withDeleteHeader()->json(
            'POST',
            route('users.teams.remove', [$team->id()]),
            $this->asData('')
        )->assertStatus(200);

        $this->withAuthHeader()->json(
            'GET',
            route('users.teams.show', [$team->id()]) . '?include=*',
            )->assertJson([]);
    }
}
