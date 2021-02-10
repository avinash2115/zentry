<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\User\CRM\CRMContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Tests\Traits\HelperTrait;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tests\IntegrationTestCase;
use UnexpectedValueException;
use Arr;

/**
 * Class CRMIntegrationTest
 */
class CRMIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;
    use AuthServiceTrait;

    /**
     * @throws Exception
     */
    public function testListDrivers(): void
    {
        $this->setToken();

        $response = $this->withAuthHeader()->json(
            'GET',
            '/users/'.$this->getUser()->identity().'/relationships/crms/drivers'
        );


        $response->assertStatus(200);
        $this->asJsonApi($response)->asJsonApiCollection()->map(function ($item) {
            self::assertTrue(Arr::has(CRMContract::AVAILABLE_DRIVERS, $item->attributes()->get('type')));
        });
    }

    /**
     * @throws NotFoundException
     * @throws NonUniqueResultException
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws Exception
     */
    public function testCreatedByService(): void
    {
        $this->setToken();
        $user = $this->getUser();

        $driver = collect(array_keys(CRMContract::AVAILABLE_DRIVERS))->random();
        $created = $this->userService__()
            ->workWith($user->identity())
            ->crmService()->connect(
                [
                    'driver' => $driver,
                    'config' => [
                        'email' => $this->randString() . '@mail.com',
                        'password' => $this->randString()
                    ],
                ]
            )
            ->readonly();

        $this->flush();


        $response = $this->withAuthHeader()->json(
            'GET',
            '/users/'.$user->identity().'/relationships/crms'
        );

        $response->assertStatus(200);

        $jsonApi = $this->asJsonApi($response)->asJsonApiCollection()->first();

        self::assertEquals($created->driver(), $jsonApi->attributes()->get('driver'));
        self::assertTrue($jsonApi->attributes()->get('active'));

    }

    /**
     * @throws Exception
     */
    public function testUnauthorizedException(): void
    {
        $response = $this->json(
            'GET',
            '/users/'.$this->randString().'/relationships/crms'
        );

        $response->assertStatus(401);
    }
}
