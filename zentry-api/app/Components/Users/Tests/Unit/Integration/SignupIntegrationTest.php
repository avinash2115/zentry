<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\User\Mutators\DTO\Mutator;
use App\Components\Users\User\Profile\Mutators\DTO\Mutator as ProfileMutator;
use App\Convention\Tests\Traits\HelperTrait;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

/**
 * Class SignupIntegrationTest
 */
class SignupIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function testSignupSuccess(): void
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
    }

    /**
     * @throws Exception
     */
    public function testSignupFail(): void
    {
        $response = $this->json(
            'POST',
            '/auth/signup',
            $this->asData(
                Mutator::TYPE,
                [
                    'email' => $this->email,
                    'password' => $this->randString(5),
                    'password_repeat' => $this->randString(10),
                ]
            )
        );

        $response->assertStatus(500);
    }
}
