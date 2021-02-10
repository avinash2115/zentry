<?php

namespace App\Components\Users\Jobs\DataProvider;

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Jobs\Base\Job;
use App\Convention\ValueObjects\Identity\Identity;
use Flusher;

/**
 * Class Synchronize
 *
 * @package App\Components\Users\Jobs\DataProvider
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
    private Identity $dataProviderIdentity;

    /**
     * @param Identity $userIdentity
     * @param Identity $dataProviderIdentity
     */
    public function __construct(Identity $userIdentity, Identity $dataProviderIdentity)
    {
        $this->userIdentity = $userIdentity;
        $this->dataProviderIdentity = $dataProviderIdentity;
    }

    /**
     * @inheritDoc
     */
    protected function _handle(): void
    {
        Flusher::open();
        $this->userService__()->workWith($this->userIdentity)->dataProviderService()->workWith($this->dataProviderIdentity)->sync();
        Flusher::flush();
        Flusher::commit();
    }
}
