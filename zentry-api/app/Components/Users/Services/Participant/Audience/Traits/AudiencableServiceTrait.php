<?php

namespace App\Components\Users\Services\Participant\Audience\Traits;

use App\Components\Users\Participant\Contracts\AudiencableContract;
use App\Components\Users\Services\Participant\Audience\AudienceServiceContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait AudiencableServiceTrait
 *
 * @package App\Components\Users\Services\Participant\Audience\Traits
 */
trait AudiencableServiceTrait
{
    /**
     * @var AudienceServiceContract | null
     */
    private ?AudienceServiceContract $audienceService__ = null;

    /**
     * @return AudienceServiceContract
     * @throws BindingResolutionException
     * @throws NotImplementedException
     */
    public function audienceService(): AudienceServiceContract
    {
        if (!$this->audienceService__ instanceof AudienceServiceContract) {
            $this->setAudienceService();
        }

        return $this->audienceService__;
    }

    /**
     * @throws BindingResolutionException
     * @throws NotImplementedException
     */
    private function setAudienceService(): void
    {
        if (!method_exists($this, '_entity') || !$this->_entity() instanceof AudiencableContract) {
            throw new NotImplementedException(__METHOD__, __CLASS__);
        }

        $this->audienceService__ = app()->make(AudienceServiceContract::class, [
            'audiencable' => $this->_entity(),
            'audiencableService' => $this,
        ]);
    }
}
