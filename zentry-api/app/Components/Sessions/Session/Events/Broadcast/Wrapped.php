<?php

namespace App\Components\Sessions\Session\Events\Broadcast;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Session\SessionDTO;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Wrapped
 *
 * @package App\Components\Sessions\Session\Events\Broadcast
 */
class Wrapped extends BroadcastEventAbstract
{
    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @var SessionDTO
     */
    private SessionDTO $sessionDTO;

    /**
     * @param SessionDTO $sessionDTO
     * @param Identity   $userIdentity
     *
     * @throws BindingResolutionException
     */
    public function __construct(SessionDTO $sessionDTO, Identity $userIdentity)
    {
        $this->withDTO($sessionDTO);
        $this->userIdentity = $userIdentity;
        $this->sessionDTO = $sessionDTO;
    }

    /**
     * @inheritDoc
     */
    public function getBroadcastChannels(): array
    {
        $channelExact = str_replace(
            [BroadcastEventAbstract::USER_CHANNEL_PARAMETER, SessionServiceContract::BROADCAST_CHANNEL_PARAMETER],
            [
                $this->userIdentity->toString(),
                $this->sessionDTO->id(),
            ],
            SessionServiceContract::BROADCAST_CHANNEL_EXACT
        );

        $channel = str_replace(
            [BroadcastEventAbstract::USER_CHANNEL_PARAMETER, SessionServiceContract::BROADCAST_CHANNEL_PARAMETER],
            [
                $this->userIdentity->toString(),
            ],
            SessionServiceContract::BROADCAST_CHANNEL_BASE
        );

        return [
            new PrivateChannel($channelExact),
            new PrivateChannel($channel),
        ];
    }
}
