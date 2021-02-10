<?php

namespace App\Components\Users\Services\Participant\Goal;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Users\Participant\Goal\GoalContract;
use App\Components\Users\Participant\Goal\GoalDTO;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Mutators\DTO\Mutator;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\CRM\Services\Traits\CRMEntityGuardTrait;
use App\Components\Users\Services\Participant\Goal\Tracker\TrackerServiceContract;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\ValueObjects\Participant\Goal\Meta;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class GoalService
 *
 * @package App\Components\Users\Services\Participant\Goal
 */
class GoalService implements GoalServiceContract
{
    use UserServiceTrait;
    use LinkParametersTrait;
    use AuthServiceTrait;
    use CRMEntityGuardTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var GoalContract | null
     */
    private ?GoalContract $entity = null;

    /**
     * @var ParticipantContract
     */
    private ParticipantContract $participant;

    /**
     * @var TrackerServiceContract|null
     */
    private ?TrackerServiceContract $trackerService = null;

    /**
     * GoalService constructor.
     *
     * @param ParticipantContract $participant
     */
    public function __construct(ParticipantContract $participant)
    {
        $this->participant = $participant;
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
     * @inheritDoc
     */
    public function workWith(string $id): GoalServiceContract
    {
        return $this->setEntity($this->_participant()->goalByIdentity(new Identity($id)));
    }

    /**
     * @return GoalContract
     * @throws PropertyNotInit
     */
    private function _entity(): GoalContract
    {
        if (!$this->entity instanceof GoalContract) {
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
     * @param GoalContract $entity
     *
     * @return GoalService
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    private function setEntity(GoalContract $entity): GoalService
    {
        $this->entity = $entity;

        $this->setTackerService();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): GoalReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): GoalDTO
    {
        $this->linkParameters__()->put(collect(['participantId' => $this->_participant()->identity()->toString()]));

        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        $this->linkParameters__()->put(collect(['participantId' => $this->_participant()->identity()->toString()]));

        return $this->listRO()->map(
            function (GoalReadonlyContract $entity) {
                return $this->_mutator()->toDTO($entity);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_participant()->goals();
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): GoalServiceContract
    {
        if (Arr::has($data, 'name')) {
            $this->_entity()->changeName(Arr::get($data, 'name'));
        }

        if (Arr::has($data, 'description')) {
            $this->_entity()->changeDescription(Arr::get($data, 'description'));
        }

        if (Arr::has($data, 'reached')) {
            $reached = filter_var(Arr::get($data, 'reached'), FILTER_VALIDATE_BOOLEAN);

            if ($reached && !$this->_entity()->isReached()) {
                $this->_entity()->reach();
            } elseif (!$reached && $this->_entity()->isReached()) {
                $this->_entity()->unReach();
            }
        }

        $this->_entity()->changeIEP();

        if (Arr::has($data, 'iep')) {
            $iep = $this->_participant()->ieps()->first(function (IEPReadonlyContract $entity) use ($data) {
                return $entity->identity()->equals(new Identity(Arr::get($data, 'iep')));
            });

            if ($iep instanceof IEPReadonlyContract) {
                $this->_entity()->changeIEP($iep);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): GoalServiceContract
    {
        $this->setEntity($this->make($data));

        $this->change(Arr::only($data, ['iep']));

        $this->_participant()->addGoal($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): GoalServiceContract
    {
        $this->checkRemoving($this->readonly());

        $this->_participant()->removeGoal($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function trackerService(): TrackerServiceContract
    {
        if (!$this->trackerService instanceof GoalServiceContract) {
            $this->setTackerService();
        }

        return $this->trackerService;
    }

    /**
     * @return GoalServiceContract
     * @throws BindingResolutionException
     */
    private function setTackerService(): GoalServiceContract
    {
        $this->trackerService = app()->make(
            TrackerServiceContract::class,
            [
                'participant' => $this->_participant(),
                'goal' => $this->readonly(),
            ]
        );

        return $this;
    }

    /**
     * @param array $data
     *
     * @return GoalContract
     * @throws BindingResolutionException
     */
    private function make(array $data): GoalContract
    {
        return app()->make(
            GoalContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'participant' => $this->_participant(),
                'name' => Arr::get($data, 'name', ''),
                'meta' => new Meta(Arr::get($data, 'meta', [])),
                'description' => Arr::get($data, 'description', ''),
            ]
        );
    }
}
