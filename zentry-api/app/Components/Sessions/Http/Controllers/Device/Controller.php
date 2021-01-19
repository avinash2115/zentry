<?php

namespace App\Components\Sessions\Http\Controllers\Device;

use App\Assistants\QR\Services\Traits\QRServiceTrait;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Device\DeviceReadonlyContract;
use App\Components\Users\Exceptions\Device\Header\NotFound;
use App\Components\Users\Exceptions\Device\TTLExpired;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Components\Users\ValueObjects\Device\ConnectingToken;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Cache;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Sessions\Http\Controllers\Device
 */
class Controller extends BaseController
{
    use SessionServiceTrait;
    use AuthServiceTrait;
    use QRServiceTrait;
    use DeviceServiceTrait;

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NonUniqueResultException|PropertyNotInit|UnexpectedValueException|NotFoundException
     */
    public function connect(JsonApi $jsonApi, string $sessionId): Response
    {
        $payload = new ConnectingPayload(
            $jsonApi->attributes()->get('type', ''),
            $jsonApi->attributes()->get('model', ''),
            $jsonApi->attributes()->get('reference', ''),
        );

        $attachedDevices = collect(Cache::get($sessionId));
        $attachedDevices->push($payload);

        Cache::set($sessionId, $attachedDevices);
        Cache::set(
            $payload->reference(),
            new ConnectingToken(
                $this->sessionService__()->workWith($sessionId)->readonly()->user()->identity(), new DateTime()
            )
        );

        return $this->sendResponse($payload);
    }

    /**
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NonUniqueResultException|PropertyNotInit|UnexpectedValueException|NotFoundException
     */
    public function disconnect(string $sessionId): Response
    {
        $deviceReference = $this->authService__()->deviceReference();

        if (!is_string($deviceReference)) {
            throw new NotFound();
        }

        $attachedDevices = collect(Cache::get($sessionId));

        $payload = $attachedDevices->first(
            static function (ConnectingPayload $payload) use ($deviceReference) {
                return $payload->reference() === $deviceReference;
            }
        );

        if ($payload instanceof ConnectingPayload) {
            Cache::forget($payload->reference());

            if (!$this->sessionService__()->workWith($sessionId)->readonly()->isEnded()) {
                Cache::set($sessionId, $attachedDevices->filter(
                    static function(ConnectingPayload $payload) use ($deviceReference) {
                        return $payload->reference() !== $deviceReference;
                }));

            }
        }

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|UnexpectedValueException|TTLExpired|NonUniqueResultException|RuntimeException
     */
    public function save(JsonApi $jsonApi, string $sessionId): Response
    {
        $deviceReference = $this->authService__()->deviceReference();

        if (!is_string($deviceReference)) {
            throw new NotFound();
        }

        try {
            if (!$this->sessionService__()->workWith($sessionId)->readonly()->startedAt()) {
                throw new RuntimeException('Trying to save device while session is active');
            }

            if ($this->deviceService__()->workWithReference($deviceReference)->readonly(
                ) instanceof DeviceReadonlyContract) {
                throw new RuntimeException('This device already attached to user');
            }
        } catch (NotFoundException $exception) {
        }

        $attachedDevices = collect(Cache::get($sessionId));

        $payload = $attachedDevices->first(
            function (ConnectingPayload $payload) use ($deviceReference) {
                return $payload->reference() === $deviceReference;
            }
        );

        if (!$payload instanceof ConnectingPayload) {
            throw new TTLExpired();
        }

        $this->deviceService__()->create($this->authService__()->user()->readonly(), $payload);

        Cache::forget($payload->reference());

        return $this->acknowledgeResponse();
    }
}
