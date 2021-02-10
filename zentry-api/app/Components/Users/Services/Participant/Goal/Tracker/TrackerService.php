<?php

namespace App\Components\Users\Services\Participant\Goal\Tracker;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Users\Participant\Goal\GoalContract;
use App\Components\Users\Participant\Goal\Tracker\TrackerContract;
use App\Components\Users\Participant\Goal\Tracker\TrackerDTO;
use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class TrackerService
 *
 * @package App\Components\Users\Services\Participant\Goal\Tracker
 */
class TrackerService implements TrackerServiceContract
{
    use UserServiceTrait;
    use LinkParametersTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var TrackerContract | null
     */
    private ?TrackerContract $entity = null;

    /**
     * @var GoalContract
     */
    private GoalContract $goal;

    /**
     * @var ParticipantReadonlyContract
     */
    private ParticipantReadonlyContract $participant;

    /**
     * @param ParticipantReadonlyContract $participant
     * @param GoalContract                $goal
     */
    public function __construct(ParticipantReadonlyContract $participant, GoalContract $goal)
    {
        $this->participant = $participant;
        $this->goal = $goal;
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
    public function workWith(string $id): TrackerServiceContract
    {
        return $this->setEntity($this->_goal()->trackerByIdentity(new Identity($id)));
    }

    /**
     * @return TrackerContract
     * @throws PropertyNotInit
     */
    private function _entity(): TrackerContract
    {
        if (!$this->entity instanceof TrackerContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param TrackerContract $entity
     *
     * @return TrackerService
     * @throws PropertyNotInit
     */
    private function setEntity(TrackerContract $entity): TrackerService
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): TrackerReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): TrackerDTO
    {
        $this->fillLinkParameters();

        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        $this->fillLinkParameters();

        return $this->listRO()->map(
            function (TrackerReadonlyContract $entity) {
                return $this->_mutator()->toDTO($entity);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_goal()->trackers();
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): TrackerServiceContract
    {
        if (Arr::has($data, 'name')) {
            $exists = $this->_goal()->trackers()->some(
                function (TrackerReadonlyContract $tracker) use ($data) {
                    return $tracker->name() === Arr::get($data, 'name') && !$tracker->identity()->equals(
                            $this->_entity()->identity()
                        );
                }
            );

            if ($exists) {
                throw new InvalidArgumentException(
                    sprintf('Tracker with name %s already exist', Arr::get($data, 'name'))
                );
            }

            $this->_entity()->changeName(Arr::get($data, 'name'));
        }

        if (Arr::has($data, 'type')) {
            $this->_entity()->changeType(Arr::get($data, 'type'));
        }

        if (Arr::has($data, 'icon')) {
            $this->_entity()->changeIcon(Arr::get($data, 'icon'));
        }

        if (Arr::has($data, 'color')) {
            $this->_entity()->changeColor(Arr::get($data, 'color'));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): TrackerServiceContract
    {
        $this->setEntity($this->make($data));
        $this->_goal()->addTracker($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function createDefault(): TrackerServiceContract
    {
        collect(self::DEFAULT_TRACKERS)->diffKeys(
            $this->_goal()->trackers()->mapWithKeys(
                function (TrackerReadonlyContract $tracker) {
                    return [$tracker->name() => $tracker];
                }
            )
        )->each(
            function (string $icon, string $name) {
                $this->create(
                    [
                        'name' => $name,
                        'type' => str_replace(['check-circle', 'times-circle', 'life-ring'], [TrackerReadonlyContract::TYPE_POSITIVE, TrackerReadonlyContract::TYPE_NEGATIVE, TrackerReadonlyContract::TYPE_NEUTRAL], $icon),
                        'icon' => $icon,
                        'color' => str_replace(['check-circle', 'times-circle', 'life-ring'], ['#28a745', '#dc3545', '#ffc107'], $icon),
                    ]
                );
            }
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): TrackerServiceContract
    {
        $this->_goal()->removeTracker($this->_entity());

        return $this;
    }

    /**
     * @return ParticipantReadonlyContract
     */
    private function _participant(): ParticipantReadonlyContract
    {
        if (!$this->participant instanceof ParticipantReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->participant;
    }

    /**
     * @return GoalContract
     */
    private function _goal(): GoalContract
    {
        if (!$this->goal instanceof GoalContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->goal;
    }

    /**
     * @param array $data
     *
     * @return TrackerContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function make(array $data): TrackerContract
    {
        return app()->make(
            TrackerContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'goal' => $this->_goal(),
                'name' => Arr::get($data, 'name', ''),
                'type' => Arr::get($data, 'type', TrackerReadonlyContract::TYPE_NEUTRAL),
                'icon' => Arr::get($data, 'icon', 'life-ring'),
                'color' => Arr::get($data, 'color', '#ffc107'),
            ]
        );
    }

    /**
     * @throws BindingResolutionException
     */
    private function fillLinkParameters(): void
    {
        $this->linkParameters__()->put(
            collect(
                [
                    'participantId' => $this->_participant()->identity()->toString(),
                    'goalId' => $this->_goal()->identity()->toString(),
                ]
            )
        );
    }
}
