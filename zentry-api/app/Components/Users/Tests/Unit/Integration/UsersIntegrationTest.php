<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Convention\Tests\Traits\HelperTrait;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\IntegrationTestCase;

/**
 * Class UsersIntegrationTest
 */
class UsersIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;
    use RefreshDatabase;
    use AuthServiceTrait;

    /**
     * @throws Exception
     */
    public function testGetCurrent(): void
    {
        $response = $this->json(
            'GET',
            '/users/current',
            );

        $response->assertStatus(401);

        $this->setToken();

        $response = $this->withAuthHeader()->json(
            'GET',
            '/users/current',
            );

        $response->assertStatus(200);

        $jsonApi = $this->asJsonApi($response);

        $this->assertEquals($jsonApi->attributes()->get('email'), $this->email);
        $this->assertEquals($jsonApi->id(), $this->authService__()->user()->identity()->toString());
        $this->assertEquals(\App\Components\Users\User\Mutators\DTO\Mutator::TYPE, $jsonApi->type());
    }
}
