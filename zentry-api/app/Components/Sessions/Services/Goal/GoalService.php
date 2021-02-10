<?php

namespace App\Components\Sessions\Services\Goal;

use App\Components\Sessions\Session\Goal\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Goal\GoalContract;
use App\Components\Sessions\Session\Goal\GoalDTO;
use App\Components\Sessions\Session\Goal\GoalReadonlyContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract as ParticipantGoalReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

/**
 * Class GoalService
 *
 * @package App\Components\Sessions\Services\Goal
 */
class GoalService implements GoalServiceContract
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var GoalContract|null
     */
    private ?GoalContract $entity = null;

    /**
     * @var SessionContract | null
     */
    private ?SessionContract $session = null;

    /**
     * GoalService constructor.
     *
     * @param SessionContract $session
     */
    public function __construct(SessionContract $session)
    {
        $this->session = $session;
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
        $this->setEntity($this->_session()->goalByIdentity(new Identity($id)));

        return $this;
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
     * @param GoalContract $entity
     *
     * @return GoalServiceContract
     */
    private function setEntity(GoalContract $entity): GoalServiceContract
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function identity(): Identity
    {
        return $this->_entity()->identity();
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
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (GoalReadonlyContract $progress) {
                return $this->_mutator()->toDTO($progress);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_session()->progress();
    }

    /**
     * @inheritDoc
     */
    public function create(
        ParticipantReadonlyContract $participant,
        ParticipantGoalReadonlyContract $goal,
        DateTime $createdAt = null
    ): GoalServiceContract {
        $entity = $this->make($participant, $goal, $createdAt);

        $this->setEntity($entity);

        $this->_session()->addGoal($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): GoalServiceContract
    {
        $this->_session()->removeGoal($this->_entity());

        return $this;
    }

    /**
     * @return SessionContract
     * @throws PropertyNotInit
     */
    private function _session(): SessionContract
    {
        if (!$this->session instanceof SessionContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->session;
    }

    /**
     * @param ParticipantReadonlyContract     $participant
     * @param ParticipantGoalReadonlyContract $goal
     * @param DateTime|null                   $createdAt
     *
     * @return GoalContract
     * @throws BindingResolutionException
     */
    private function make(
        ParticipantReadonlyContract $participant,
        ParticipantGoalReadonlyContract $goal,
        DateTime $createdAt = null
    ): GoalContract {
        return app()->make(
            GoalContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'session' => $this->_session(),
                'participant' => $participant,
                'goal' => $goal,
                'createdAt' => $createdAt instanceof DateTime ? $createdAt : new DateTime(),
            ]
        );
    }
}
