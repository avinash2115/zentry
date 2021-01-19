<?php

namespace App\Components\Users\Services\Device;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Users\Device\DeviceDTO;
use App\Components\Users\Device\DeviceReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\CountableContract;
use App\Convention\Services\Contracts\FilterableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Interface DeviceServiceContract
 *
 * @package App\Components\Users\Services\Device
 */
interface DeviceServiceContract extends FilterableContract, CountableContract
{
    public const BROADCAST_CHANNEL = BroadcastEventAbstract::USER_CHANNEL_BASE . '.devices';

    public const ROUTE_ADD_DEVICE_BY_TOKEN = 'devices.connect_by_token';

    /**
     * @param string $id
     *
     * @return DeviceServiceContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function workWith(string $id): DeviceServiceContract;

    /**
     * @param string $reference
     *
     * @return DeviceServiceContract
     * @throws BindingResolutionException|NotFoundException|NonUniqueResultException
     */
    public function workWithReference(string $reference): DeviceServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return DeviceReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): DeviceReadonlyContract;

    /**
     * @return DeviceDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): DeviceDTO;

    /**
     * @param UserReadonlyContract $user
     * @param ConnectingPayload    $payload
     *
     * @return DeviceServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException|InvalidArgumentException
     */
    public function create(UserReadonlyContract $user, ConnectingPayload $payload): DeviceServiceContract;

    /**
     * @throws BindingResolutionException|NotFoundException
     */
    public function listRO(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException|NotFoundException
     */
    public function list(): Collection;

    /**
     * @return DeviceServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function remove(): DeviceServiceContract;
}
