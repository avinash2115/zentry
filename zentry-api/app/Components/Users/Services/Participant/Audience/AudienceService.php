<?php

namespace App\Components\Users\Services\Participant\Audience;

use App\Components\Users\Participant\Contracts\AudiencableContract;
use App\Components\Users\Participant\Mutators\DTO\Mutator;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

/**
 * Class AudienceService
 *
 * @package App\Components\Users\Services\Participant\Audience
 */
class AudienceService implements AudienceServiceContract
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var AudiencableServiceContract
     */
    private AudiencableServiceContract $audiencableService;

    /**
     * @var AudiencableContract
     */
    private AudiencableContract $audiencable;

    /**
     * AudienceService constructor.
     *
     * @param AudiencableServiceContract $audiencableService
     * @param AudiencableContract        $audiencable
     */
    public function __construct(AudiencableServiceContract $audiencableService, AudiencableContract $audiencable)
    {
        $this->audiencableService = $audiencableService;
        $this->audiencable = $audiencable;
    }

    /**
     * @return self
     * @throws BindingResolutionException
     */
    private function setMutator(): self
    {
        if (!$this->mutator instanceof Mutator) {
            $this->mutator = app()->make(Mutator::class);
        }

        return $this;
    }

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function _mutator(): Mutator
    {
        $this->setMutator();

        return $this->mutator;
    }

    /**
     * @inheritDoc
     */
    public function add(ParticipantReadonlyContract $participant): AudienceServiceContract
    {
        $this->_audiencable()->addParticipant($participant);

        $this->_audiencableService()->participantAdded($this->_mutator()->toDTO($participant));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function kick(ParticipantReadonlyContract $participant): AudienceServiceContract
    {
        $this->_audiencable()->removeParticipant($participant);

        $this->_audiencableService()->participantRemoved($this->_mutator()->toDTO($participant));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_audiencable()->participants();
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (ParticipantReadonlyContract $participant) {
                return $this->_mutator()->toDTO($participant);
            }
        );
    }

    /**
     * @return AudiencableContract
     * @throws PropertyNotInit
     */
    private function _audiencable(): AudiencableContract
    {
        if (!$this->audiencable instanceof AudiencableContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->audiencable;
    }

    /**
     * @return AudiencableServiceContract
     * @throws PropertyNotInit
     */
    private function _audiencableService(): AudiencableServiceContract
    {
        if (!$this->audiencableService instanceof AudiencableServiceContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->audiencableService;
    }
}
