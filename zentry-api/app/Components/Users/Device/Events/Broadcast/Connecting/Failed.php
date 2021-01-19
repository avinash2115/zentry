<?php

namespace App\Components\Users\Device\Events\Broadcast\Connecting;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Users\Services\Device\DeviceServiceContract;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Failed
 * @package App\Components\Users\Device\Events\Broadcast\Connecting
 */
class Failed extends BroadcastEventAbstract
{
    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @param ConnectingPayload $connectingPayload
     * @param Identity          $userIdentity
     *
     * @throws BindingResolutionException
     */
    public function __construct(ConnectingPayload $connectingPayload, Identity $userIdentity)
    {
        $this->withDTO($connectingPayload);
        $this->userIdentity = $userIdentity;
    }

    /**
     * @return array
     */
    public function getBroadcastChannels(): array
    {
        $channel = str_replace(
            BroadcastEventAbstract::USER_CHANNEL_PARAMETER,
            $this->userIdentity->toString(),
            DeviceServiceContract::BROADCAST_CHANNEL
        );

        return [new PrivateChannel($channel)];
    }


    /**
     * @inheritDoc
     */
    public function broadcastAs(): string
    {
        parent::broadcastAs();

        return 'connecting_failed';
    }
}
