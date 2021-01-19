<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\User\Mutators\DTO\Mutator;
use App\Components\Users\User\Profile\Mutators\DTO\Mutator as ProfileMutator;
use App\Convention\Tests\Traits\HelperTrait;
use Exception;
use Illuminate\Support\Arr;
use Tests\IntegrationTestCase;

/**
 * Class LoginIntegrationTest
 */
class LoginIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;

    /**
     * @throws Exception
     */
    public function testLoginFail(): void
    {
        $response = $this->json(
            'POST',
            '/auth/login',
            $this->asData(
                Mutator::TYPE,
                [
                    'email' => $this->email,
                    'password' => $this->randString(),
                ]
            )
        );

        $response->assertStatus(404);
    }

    /**
     * @throws Exception
     */
    public function testLoginSuccess(): void
    {
        $password = $this->randString();
        $response = $this->json(
            'POST',
            '/auth/signup',
            $this->asData(
                Mutator::TYPE,
                [
                    'email' => $this->email,
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

        $response->assertStatus(201);

        $response = $this->json(
            'POST',
            '/auth/login',
            $this->asData(
                Mutator::TYPE,
                [
                    'email' => $this->email,
                    'password' => $password,
                    'password_repeat' => $password,
                ]
            )
        );

        $response->assertStatus(200);

        $response = $this->withHeaders(
            [
                'Authorization' => 'Bearer ' . Arr::get($response, 'data.attributes.token'),
            ]
        )->json('GET', '/auth/logout');

        $response->assertStatus(200)->assertJson(['acknowledge' => true]);
    }
}
