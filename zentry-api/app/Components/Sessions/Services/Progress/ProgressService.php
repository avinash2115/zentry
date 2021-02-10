<?php

namespace App\Components\Sessions\Services\Progress;

use App\Assistants\Events\EventRegistry;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Session\Progress\Events\Broadcast\Created;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Progress\Events\Broadcast\Removed;
use App\Components\Sessions\Session\Progress\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Progress\ProgressContract;
use App\Components\Sessions\Session\Progress\ProgressDTO;
use App\Components\Sessions\Session\Progress\ProgressReadonlyContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\ValueObjects\Progress\Payload;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

/**
 * Class ProgressService
 *
 * @package App\Components\Sessions\Services\Progress
 */
class ProgressService implements ProgressServiceContract
{
    use LinkParametersTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var ProgressContract|null
     */
    private ?ProgressContract $entity = null;

    /**
     * @var SessionContract | null
     */
    private ?SessionContract $session = null;

    /**
     * ProgressService constructor.
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
    public function workWith(string $id): ProgressServiceContract
    {
        $this->setEntity($this->_session()->progressByIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @return ProgressContract
     * @throws PropertyNotInit
     */
    private function _entity(): ProgressContract
    {
        if (!$this->entity instanceof ProgressContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param ProgressContract $entity
     *
     * @return ProgressServiceContract
     */
    private function setEntity(ProgressContract $entity): ProgressServiceContract
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
    public function readonly(): ProgressReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): ProgressDTO
    {
        $this->fillLinkParameters($this->_entity());

        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (ProgressReadonlyContract $entity) {
                $this->fillLinkParameters($entity);

                return $this->_mutator()->toDTO($entity);
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
    public function create(Payload $payload, PoiReadonlyContract $poi = null): ProgressServiceContract
    {
        $entity = $this->make($payload, $poi);

        $this->setEntity($entity);

        $this->_session()->addProgress($entity);

        app()->make(EventRegistry::class)->registerBroadcast(
            new Created($this->dto(), $this->_session(), $this->_session()->user()->identity())
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): ProgressServiceContract
    {
        $this->_session()->removeProgress($this->_entity());

        app()->make(EventRegistry::class)->registerBroadcast(
            new Removed($this->dto(), $this->_session(), $this->_session()->user()->identity())
        );

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
     * @param Payload                  $payload
     * @param PoiReadonlyContract|null $poi
     *
     * @return ProgressContract
     * @throws BindingResolutionException
     */
    private function make(Payload $payload, PoiReadonlyContract $poi = null): ProgressContract
    {
        return app()->make(
            ProgressContract::class,
            [
                'payload' => $payload,
                'session' => $this->_session(),
                'poi' => $poi,
            ]
        );
    }

    /**
     * @param ProgressReadonlyContract $entity
     *
     * @throws BindingResolutionException
     */
    private function fillLinkParameters(ProgressReadonlyContract $entity): void
    {
        $this->linkParameters__()->put(
            collect(
                [
                    'sessionId' => $this->_session()->identity()->toString(),
                    'participantId' => $entity->participant()->identity()->toString(),
                    'goalId' => $entity->goal()->identity()->toString(),
                ]
            )
        );
    }
}
