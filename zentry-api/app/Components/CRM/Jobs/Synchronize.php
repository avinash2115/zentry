<?php

namespace App\Components\CRM\Jobs;

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Jobs\Base\Job;
use App\Convention\Jobs\QueuesConstants;
use App\Convention\ValueObjects\Identity\Identity;
use Flusher;

/**
 * Class Synchronize
 *
 * @package App\Components\CRM\Jobs
 */
class Synchronize extends Job
{
    use UserServiceTrait;

    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @var Identity
     */
    private Identity $crmIdentity;

    /**
     * @param Identity $userIdentity
     * @param Identity $crmIdentity
     */
    public function __construct(Identity $userIdentity, Identity $crmIdentity)
    {
        $this->userIdentity = $userIdentity;
        $this->crmIdentity = $crmIdentity;

        $this->queue = QueuesConstants::QUEUE_CRM_SYNC;
    }

    /**
     * @inheritDoc
     */
    protected function _handle(): void
    {
        Flusher::open();
        $this->userService__()->workWith($this->userIdentity)->crmService()->workWith($this->crmIdentity)->sync();
        Flusher::flush();
        Flusher::commit();
    }
}