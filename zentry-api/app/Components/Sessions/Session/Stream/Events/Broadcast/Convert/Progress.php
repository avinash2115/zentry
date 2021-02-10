<?php

namespace App\Components\Sessions\Session\Stream\Events\Broadcast\Convert;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Session\Stream\StreamDTO;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Progress
 *
 * @package App\Components\Sessions\Session\Stream\Events\Broadcast\Convert
 */
class Progress extends BroadcastEventAbstract
{
    /**
     * @var SessionReadonlyContract
     */
    private SessionReadonlyContract $session;

    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @param StreamDTO               $streamDTO
     * @param SessionReadonlyContract $session
     * @param Identity                $userIdentity
     *
     * @throws BindingResolutionException
     */
    public function __construct(StreamDTO $streamDTO, SessionReadonlyContract $session, Identity $userIdentity)
    {
        $this->withDTO($streamDTO);
        $this->session = $session;
        $this->userIdentity = $userIdentity;
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
                $this->session->identity()->toString(),
            ],
            SessionServiceContract::BROADCAST_CHANNEL_EXACT
        );

        return [new PrivateChannel($channel)];
    }

    /**
     * @inheritDoc
     */
    public function broadcastAs(): string
    {
        parent::broadcastAs();

        return 'stream_convert_progress';
    }
}
