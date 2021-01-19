<?php

namespace App\Components\Users\Services\Participant\IEP;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\CRM\Services\Traits\CRMEntityGuardTrait;
use App\Components\Users\Participant\IEP\IEPContract;
use App\Components\Users\Participant\IEP\IEPDTO;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Components\Users\Participant\IEP\Mutators\DTO\Mutator;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class IEPService
 *
 * @package App\Components\Users\Services\Participant\IEP
 */
class IEPService implements IEPServiceContract
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
     * @var IEPContract | null
     */
    private ?IEPContract $entity = null;

    /**
     * @var ParticipantContract
     */
    private ParticipantContract $participant;

    /**
     * IEPService constructor.
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
    public function workWith(string $id): IEPServiceContract
    {
        return $this->setEntity($this->_participant()->IEPByIdentity(new Identity($id)));
    }

    /**
     * @return IEPContract
     * @throws PropertyNotInit
     */
    private function _entity(): IEPContract
    {
        if (!$this->entity instanceof IEPContract) {
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
     * @param IEPContract $entity
     *
     * @return IEPService
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    private function setEntity(IEPContract $entity): IEPService
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): IEPReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): IEPDTO
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
            function (IEPReadonlyContract $entity) {
                return $this->_mutator()->toDTO($entity);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_participant()->ieps();
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): IEPServiceContract
    {
        if (Arr::has($data, 'date_actual')) {
            $this->_entity()->changeDateActual(Carbon::parse(Arr::get($data, 'date_actual'))->toDateTime());
        }

        if (Arr::has($data, 'date_reeval')) {
            $this->_entity()->changeDateReeval(Carbon::parse(Arr::get($data, 'date_reeval'))->toDateTime());
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): IEPServiceContract
    {
        $this->setEntity($this->make($data));
        $this->_participant()->addIEP($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): IEPServiceContract
    {
        $this->checkRemoving($this->readonly());

        $this->_participant()->removeIEP($this->_entity());

        return $this;
    }

    /**
     * @param array $data
     *
     * @return IEPContract
     * @throws BindingResolutionException
     */
    private function make(array $data): IEPContract
    {
        return app()->make(
            IEPContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'participant' => $this->_participant(),
                'dateActual' => Carbon::parse(Arr::get($data, 'date_actual'))->toDateTime(),
                'dateReeval' => Carbon::parse(Arr::get($data, 'date_reeval'))->toDateTime(),
            ]
        );
    }
}
