<?php

namespace App\Components\Sessions\Services\Poi\Participant;

use App\Components\Sessions\Session\Poi\Participant\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Poi\Participant\ParticipantContract;
use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract;
use App\Components\Sessions\Session\Poi\PoiContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Users\Participant\ParticipantReadonlyContract as UsersParticipantReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Class ParticipantService
 *
 * @package App\Components\Sessions\Services\Poi\Participant
 */
class ParticipantService implements ParticipantServiceContract
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var ParticipantContract|null
     */
    private ?ParticipantContract $entity = null;

    /**
     * @var SessionContract
     */
    private SessionContract $session;

    /**
     * @var PoiContract
     */
    private PoiContract $poi;

    /**
     * @param SessionContract $session
     * @param PoiContract     $poi
     */
    public function __construct(SessionContract $session, PoiContract $poi)
    {
        $this->session = $session;
        $this->poi = $poi;
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
    public function workWith(string $id): ParticipantServiceContract
    {
        $this->setEntity($this->_poi()->participantByIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @return ParticipantContract
     * @throws PropertyNotInit
     */
    private function _entity(): ParticipantContract
    {
        if (!$this->entity instanceof ParticipantContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param ParticipantContract $entity
     *
     * @return ParticipantService
     */
    private function setEntity(ParticipantContract $entity): ParticipantService
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): ParticipantReadonlyContract
    {
        return $this->_entity();
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
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_poi()->participants();
    }

    /**
     * @inheritDoc
     */
    public function add(UsersParticipantReadonlyContract $participant, array $data): ParticipantServiceContract
    {
        if (!$this->_session()->participants()->has($participant->identity()->toString())) {
            throw new RuntimeException('Unknown participant. You should add participant to the session first.');
        }

        $entity = $this->make($participant, $data);

        $this->setEntity($entity);

        $this->_poi()->addParticipant($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): ParticipantServiceContract
    {
        $this->_poi()->removeParticipant($this->_entity());

        return $this;
    }

    /**
     * @param UsersParticipantReadonlyContract $participant
     * @param array                            $data
     *
     * @return ParticipantContract
     * @throws Exception
     */
    private function make(UsersParticipantReadonlyContract $participant, array $data): ParticipantContract
    {
        return app()->make(
            ParticipantContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'poi' => $this->_poi(),
                'participant' => $participant,
                'startedAt' => new DateTime(Arr::get($data, 'started_at')),
                'endedAt' => new DateTime(Arr::get($data, 'ended_at')),
            ]
        );
    }

    /**
     * @return PoiContract
     * @throws PropertyNotInit
     */
    private function _poi(): PoiContract
    {
        if (!$this->poi instanceof PoiContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->poi;
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
}
