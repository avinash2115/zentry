<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\Device\DeviceDTO;
use App\Components\Users\Device\Mutators\DTO\Mutator;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Http\Middleware\Access\Device\Authenticate;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tests\IntegrationTestCase;
use UnexpectedValueException;

/**
 * Class DeviceIntegrationTest
 */
class DeviceIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;
    use DeviceServiceTrait;
    use AuthServiceTrait;

    /**
     * @throws Exception
     */
    public function testEmptySuccessList(): void
    {
        $response = $this->withAuthHeader()->json(
            'GET',
            '/devices'
        );

        $response->assertStatus(200);
        $response->assertJson([]);

        $response = $this->withAuthHeader()->json(
            'GET',
            '/devices/qr'
        );
        $response->assertStatus(200);
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

        $created = $this->deviceService__()->create(
            $this->authService__()->user()->readonly(),
            new ConnectingPayload(
                $this->randString(), $this->randString(), $this->randString()
            )
        )->readonly();

        $this->flush();

        $response = $this->withAuthHeader()->json(
            'GET',
            '/devices/qr'
        );
        $response->assertStatus(200);

        $response = $this->withAuthHeader()->json(
            'GET',
            '/devices'
        );

        $response->assertStatus(200);
        $jsonApi = $this->asJsonApi($response)->asJsonApiCollection()->first();

        self::assertEquals($created->identity()->toString(), $jsonApi->id());
        self::assertEquals(Mutator::TYPE, $jsonApi->type());
        self::assertEquals($created->model(), $jsonApi->attributes()->get('model'));
        self::assertEquals($created->reference(), $jsonApi->attributes()->get('reference'));
        self::assertEquals($created->type(), $jsonApi->attributes()->get('type'));

        $response->assertJson([]);

        $response = $this->withAuthHeader()->json(
            'GET',
            route(DeviceDTO::ROUTE_NAME_SHOW, $created->identity()->toString())
        );

        $response->assertStatus(200);

        $this->withAuthHeader()->json(
            'GET',
            route(DeviceDTO::ROUTE_NAME_SHOW, IdentityGenerator::next()->toString())
        )->assertStatus(404);

        $this->withAuthHeader()->json(
            'POST',
            route(DeviceDTO::ROUTE_NAME_SHOW, $created->identity()->toString()),
            $this->asData('', [])
        )->assertStatus(405);

        $response = $this->withAuthHeader()->withHeaders(
            [
                'X-HTTP-METHOD-OVERRIDE' => 'DELETE',
            ]
        )->json(
            'POST',
            route(DeviceDTO::ROUTE_NAME_SHOW, $created->identity()->toString()),
            $this->asData('', [])
        );

        $response->assertStatus(200)->assertJson(['acknowledge' => true]);
    }

    /**
     * @throws NotFoundException
     * @throws NonUniqueResultException
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws Exception
     */
    public function testRemoveByReference(): void
    {
        $this->setToken();

        $created = $this->deviceService__()->create(
            $this->authService__()->user()->readonly(),
            new ConnectingPayload(
                $this->randString(), $this->randString(), $this->randString()
            )
        )->readonly();

        $this->flush();

        $this->withAuthHeader()->withHeaders(
            [
                'X-HTTP-METHOD-OVERRIDE' => 'DELETE',

            ]
        )->json(
            'POST',
            route('devices.remove_current'),
            $this->asData('', [])
        )->assertStatus(500);

        $response = $this->withHeaders(
            [
                Authenticate::HEADER => $created->reference(),
                'X-HTTP-METHOD-OVERRIDE' => 'DELETE',

            ]
        )->json(
            'POST',
            route('devices.remove_current'),
            $this->asData('', [])
        );

        $response->assertStatus(200)->assertJson(['acknowledge' => true]);
    }

    /**
     * @throws Exception
     */
    public function testUnauthorizedException(): void
    {
        $response = $this->json(
            'GET',
            '/devices'
        );

        $response->assertStatus(401);
    }
}
