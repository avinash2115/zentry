<?php

namespace App\Components\Sessions\Session\Progress\Events\Broadcast;

use App\Assistants\Events\BroadcastEventAbstract;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Session\Progress\ProgressDTO;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Created
 *
 * @package App\Components\Sessions\Session\Progress\Events\Broadcast
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
     * @param ProgressDTO             $dto
     * @param SessionReadonlyContract $session
     * @param Identity                $userIdentity
     *
     * @throws BindingResolutionException
     */
    public function __construct(ProgressDTO $dto, SessionReadonlyContract $session, Identity $userIdentity)
    {
        $this->withDTO($dto);
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

        return 'progress_created';
    }
}
