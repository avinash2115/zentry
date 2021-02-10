<?php

namespace App\Components\Users\Services\Participant\Therapy;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Participant\Therapy\Mutators\DTO\Mutator;
use App\Components\Users\Participant\Therapy\TherapyContract;
use App\Components\Users\Participant\Therapy\TherapyDTO;
use App\Components\Users\Participant\Therapy\TherapyReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;

/**
 * Class TherapyService
 *
 * @package App\Components\Users\Services\Participant\Therapy
 */
class TherapyService implements TherapyServiceContract
{
    use UserServiceTrait;
    use LinkParametersTrait;
    use AuthServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var TherapyContract | null
     */
    private ?TherapyContract $entity = null;

    /**
     * @var ParticipantContract
     */
    private ParticipantContract $participant;

    /**
     * TherapyService constructor.
     *
     * @param ParticipantContract $participant
     */
    public function __construct(ParticipantContract $participant)
    {
        $this->participant = $participant;
        $this->setEntity($this->_participant()->therapyWritable());
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
     * @return TherapyContract
     * @throws PropertyNotInit
     */
    private function _entity(): TherapyContract
    {
        if (!$this->entity instanceof TherapyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @return ParticipantContract
     * @throws PropertyNotInit
     */
    private function _participant(): ParticipantContract
    {
        if (!$this->participant instanceof ParticipantContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->participant;
    }

    /**
     * @param TherapyContract $entity
     *
     * @return TherapyService
     * @throws PropertyNotInit
     */
    private function setEntity(TherapyContract $entity): TherapyService
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): TherapyReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): TherapyDTO
    {
        $this->linkParameters__()->put(collect(['participantId' => $this->_participant()->identity()->toString()]));

        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): TherapyServiceContract
    {
        if (Arr::has($data, 'diagnosis')) {
            $this->_entity()->changeDiagnosis((string)Arr::get($data, 'diagnosis'));
        }

        if (Arr::has($data, 'frequency')) {
            $this->_entity()->changeFrequency((string)Arr::get($data, 'frequency'));
        }

        if (Arr::has($data, 'eligibility')) {
            $this->_entity()->changeEligibility((string)Arr::get($data, 'eligibility'));
        }

        if (Arr::has($data, 'sessions_amount_planned')) {
            $this->_entity()->changeSessionsAmountPlanned((int)Arr::get($data, 'sessions_amount_planned',0));
        }

        if (Arr::has($data, 'treatment_amount_planned')) {
            $this->_entity()->changeTreatmentAmountPlanned((int)Arr::get($data, 'treatment_amount_planned',0));
        }

        if (Arr::has($data, 'notes')) {
            $this->_entity()->changeNotes((string)Arr::get($data, 'notes'));
        }

        if (Arr::has($data, 'private_notes')) {
            $this->_entity()->changePrivateNotes((string)Arr::get($data, 'private_notes'));
        }

        return $this;
    }
}
