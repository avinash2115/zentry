<?php

namespace App\Components\Sessions\Session\Events\Broadcast\Participant;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Users\Participant\ParticipantDTO;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Removed
 *
 * @package App\Components\Sessions\Session\Events\Broadcast\Participant
 */
class Removed extends BroadcastEventAbstract
{
    /**
     * @var Identity
     */
    private Identity $sessionIdentity;

    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @param ParticipantDTO $participantDTO
     * @param Identity       $sessionIdentity
     * @param Identity       $userIdentity
     *
     * @throws BindingResolutionException
     */
    public function __construct(ParticipantDTO $participantDTO, Identity $sessionIdentity, Identity $userIdentity)
    {
        $this->withDTO($participantDTO);
        $this->sessionIdentity = $sessionIdentity;
        $this->userIdentity = $userIdentity;
    }

    /**
     * @inheritDoc
     */
    public function broadcastAs(): string
    {
        parent::broadcastAs();

        return 'participant_removed';
    }

    /**
     * @inheritDoc
     */
    public function getBroadcastChannels(): array
    {
        $channel = str_replace(
            [BroadcastEventAbstract::USER_CHANNEL_PARAMETER, SessionServiceContract::BROADCAST_CHANNEL_PARAMETER],
            [
                $this->userIdentity->toString(),
                $this->sessionIdentity->toString(),
            ],
            SessionServiceContract::BROADCAST_CHANNEL_EXACT
        );

        return [new PrivateChannel($channel)];
    }
}
