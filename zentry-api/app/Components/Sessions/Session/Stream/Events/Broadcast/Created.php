<?php

namespace App\Components\Sessions\Session\Stream\Events\Broadcast;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Session\Stream\StreamDTO;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Created
 *
 * @package App\Components\Sessions\Session\Stream\Events\Broadcast
 */
class Created extends BroadcastEventAbstract
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
     * @param StreamDTO               $poi
     * @param SessionReadonlyContract $session
     *
     * @throws BindingResolutionException
     */
    public function __construct(StreamDTO $poi, SessionReadonlyContract $session)
    {
        $this->withDTO($poi);
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function getBroadcastChannels(): array
    {
        $channel = str_replace(
            [BroadcastEventAbstract::USER_CHANNEL_PARAMETER, SessionServiceContract::BROADCAST_CHANNEL_PARAMETER],
            [
                $this->session->user()->identity()->toString(),
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

        return 'stream_created';
    }
}