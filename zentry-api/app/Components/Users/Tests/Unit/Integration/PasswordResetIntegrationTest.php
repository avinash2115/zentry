<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\PasswordReset\PasswordResetContract;
use App\Components\Users\PasswordReset\Repository\PasswordResetRepositoryContract;
use App\Components\Users\User\Mutators\DTO\Mutator;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;
use UnexpectedValueException;

/**
 * Class PasswordResetIntegrationTest
 */
class PasswordResetIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function testCreate(): void
    {
        $this->setToken();

        $response = $this->json(
            'POST',
            '/password_reset',
            [
                'data' => [
                    'type' => Mutator::TYPE,
                    'attributes' => [
                        'email' => $this->email,
                    ],
                ],
            ]
        );

        $response->assertStatus(201);
        $response->assertJson(['acknowledge' => true]);

        $passwordReset = $this->app->make(PasswordResetRepositoryContract::class)->getOne();
        if (!$passwordReset instanceof PasswordResetContract) {
            throw new UnexpectedValueException();
        }

        $response = $this->json(
            'GET',
            '/password_reset/' . $passwordReset->identity()->toString()
        );

        $response->assertStatus(200);
        $data = json_decode($response->content(), true);
        $jsonApi = new JsonApi(collect($data));

        $this->assertEquals($passwordReset->identity()->toString(), $jsonApi->id());
        $this->assertEquals(\App\Components\Users\PasswordReset\Mutators\DTO\Mutator::TYPE, $jsonApi->type());

        $newPassword = $this->randString(10);

        $this->json(
            'POST',
            '/password_reset/' . IdentityGenerator::next()->toString(),
            $this->asData(
                \App\Components\Users\PasswordReset\Mutators\DTO\Mutator::TYPE,
                [
                    'password' => $newPassword,
                    'password_repeat' => $newPassword,
                ]
            )
        )->assertStatus(404);

        $this->json(
            'POST',
            '/password_reset/' . $passwordReset->identity()->toString(),
            $this->asData(
                \App\Components\Users\PasswordReset\Mutators\DTO\Mutator::TYPE,
                [
                    'password' => $this->randString(),
                    'password_repeat' => $this->randString(),
                ]
            )
        )->assertStatus(500);

        $response = $this->json(
            'POST',
            '/password_reset/' . $passwordReset->identity()->toString(),
            $this->asData(
                \App\Components\Users\PasswordReset\Mutators\DTO\Mutator::TYPE,
                [
                    'password' => $newPassword,
                    'password_repeat' => $newPassword,
                ]
            )
        );

        $response->assertStatus(200);
    }
}
