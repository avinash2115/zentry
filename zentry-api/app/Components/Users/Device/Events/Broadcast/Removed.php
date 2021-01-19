<?php

namespace App\Components\Users\Device\Events\Broadcast;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Users\Device\DeviceDTO;
use App\Components\Users\Services\Device\DeviceServiceContract;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Removed
 *
 * @package App\Components\Users\Device\Events\Broadcast
 */
class Removed extends BroadcastEventAbstract
{
    /**
     * @var DeviceDTO
     */
    private DeviceDTO $deviceDTO;

    /**
     * @param DeviceDTO $dto
     *
     * @throws BindingResolutionException
     */
    public function __construct(DeviceDTO $dto)
    {
        $this->deviceDTO = $dto;

        $this->withDTO($dto);
    }

    /**
     * @return array
     */
    public function getBroadcastChannels(): array
    {
        $channel = str_replace(
            BroadcastEventAbstract::USER_CHANNEL_PARAMETER,
            $this->deviceDTO->user->id(),
            DeviceServiceContract::BROADCAST_CHANNEL
        );

        return [new PrivateChannel($channel)];
    }
}