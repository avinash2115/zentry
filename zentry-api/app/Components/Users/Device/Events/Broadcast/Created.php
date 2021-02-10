<?php

namespace App\Components\Users\Device\Events\Broadcast;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Users\Device\DeviceDTO;
use App\Components\Users\Services\Device\DeviceServiceContract;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Created
 *
 * @package App\Components\Users\Device\Events\Broadcast
 */
class Created extends BroadcastEventAbstract
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

        $this->withDTO($this->deviceDTO);
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