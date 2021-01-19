<?php

namespace App\Components\Sessions\Jobs\Elastic\Indexing;

use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Convention\Jobs\Base\Job;
use App\Convention\ValueObjects\Identity\Identity;
use Flusher;

/**
 * Class Reindex
 *
 * @package App\Components\Sessions\Jobs\Elastic\Indexing
 */
class Reindex extends Job
{
    use SessionServiceTrait;

    /**
     * @var Identity
     */
    private Identity $participantIdentity;

    /**
     * @param Identity $participantIdentity
     */
    public function __construct(Identity $participantIdentity)
    {
        $this->participantIdentity = $participantIdentity;
    }

    /**
     * @inheritDoc
     */
    protected function _handle(): void
    {
        $this->sessionService__()->applyFilters(
            [
                'participants' => [
                    'collection' => [$this->participantIdentity->toString()],
                ],
            ]
        );

        $this->sessionService__()->listRO()->each(function(SessionReadonlyContract $session) {
            $this->sessionService__()->workWith($session->identity())->stateChanged();

            Flusher::commit(true);
            Flusher::clear();
        });
    }
}
