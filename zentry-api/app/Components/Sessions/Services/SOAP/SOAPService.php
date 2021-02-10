<?php

namespace App\Components\Sessions\Services\SOAP;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\SOAP\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\SOAP\SOAPContract;
use App\Components\Sessions\Session\SOAP\SOAPDTO;
use App\Components\Sessions\Session\SOAP\SOAPReadonlyContract;
use App\Components\Sessions\ValueObjects\SOAP\Payload;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * Class SOAPService
 *
 * @package App\Components\Sessions\Services\SOAP
 */
class SOAPService implements SOAPServiceContract
{
    use FilterableTrait;
    use LinkParametersTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var SOAPContract | null
     */
    private ?SOAPContract $entity = null;

    /**
     * @var SessionServiceContract
     */
    private SessionServiceContract $sessionService;

    /**
     * @var SessionContract
     */
    private SessionContract $session;

    /**
     * @param SessionServiceContract $sessionService
     * @param SessionContract        $session
     */
    public function __construct(SessionServiceContract $sessionService, SessionContract $session)
    {
        $this->sessionService = $sessionService;
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
     * @return SOAPContract
     * @throws PropertyNotInit
     */
    private function _entity(): SOAPContract
    {
        if (!$this->entity instanceof SOAPContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param SOAPContract $entity
     *
     * @return SOAPServiceContract
     */
    private function setEntity(SOAPContract $entity): SOAPServiceContract
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): SOAPServiceContract
    {
        $this->setEntity($this->_session()->SOAPByIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): SOAPReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): SOAPDTO
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
            function (SOAPReadonlyContract $entity) {
                return $this->_mutator()->toDTO($entity);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_session()->soaps();
    }

    /**
     * @inheritDoc
     */
    public function create(Payload $payload): SOAPServiceContract
    {
        $this->setEntity($this->make($payload));
        $this->_session()->addSOAP($this->_entity());

        return $this;
    }

    /**
     * @param Payload $payload
     *
     * @return SOAPContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function make(Payload $payload): SOAPContract
    {
        return app()->make(
            SOAPContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'session' => $this->_session(),
                'payload' => $payload,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): SOAPServiceContract
    {
        if (Arr::has($data, 'present')) {
            if (filter_var(Arr::get($data, 'present'), FILTER_VALIDATE_BOOLEAN)) {
                $this->_entity()->present();
            } else {
                $this->_entity()->absent();
            }
        }

        if (Arr::has($data, 'rate')) {
            $this->_entity()->changeRate((string)Arr::get($data, 'rate'));
        }

        if (Arr::has($data, 'activity')) {
            $this->_entity()->changeActivity((string)Arr::get($data, 'activity'));
        }

        if (Arr::has($data, 'note')) {
            $this->_entity()->changeNote((string)Arr::get($data, 'note'));
        }

        if (Arr::has($data, 'plan')) {
            $this->_entity()->changePlan((string)Arr::get($data, 'plan'));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): SOAPServiceContract
    {
        $this->_session()->removeSOAP($this->_entity());

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
     * @return SessionServiceContract
     * @throws PropertyNotInit
     */
    private function _sessionService(): SessionServiceContract
    {
        if (!$this->sessionService instanceof SessionServiceContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->sessionService;
    }

    /**
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function fillLinkParameters(): void
    {
        $this->linkParameters__()->put(
            collect(
                [
                    'sessionId' => $this->_session()->identity()->toString(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function identity(): Identity
    {
        return $this->_entity()->identity();
    }
}
