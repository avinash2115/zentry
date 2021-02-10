<?php

namespace App\Components\Users\Services\Team\Request;

use App\Assistants\Events\EventRegistry;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Team\Request\Events\Applied;
use App\Components\Users\Team\Request\Events\Created;
use App\Components\Users\Team\Request\Events\Rejected;
use App\Components\Users\Team\Request\Mutators\DTO\Mutator;
use App\Components\Users\Team\Request\RequestContract;
use App\Components\Users\Team\Request\RequestDTO;
use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\Team\TeamContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Log;
use RuntimeException;

/**
 * Class RequestService
 *
 * @package App\Components\Users\Services\Team\Request
 */
class RequestService implements RequestServiceContract
{
    use AuthServiceTrait;
    use LinkParametersTrait;

    public const LINK_PLACEHOLDER = '{id}';

    /**
     * @var RequestContract|null
     */
    private ?RequestContract $entity = null;

    /**
     * @var TeamContract
     */
    private TeamContract $team;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * RequestService constructor.
     *
     * @param TeamContract $team
     */
    public function __construct(TeamContract $team)
    {
        $this->team = $team;
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
    public function workWith(string $id): RequestServiceContract
    {
        return $this->setEntity($this->team->requestByIdentity(new Identity($id)));
    }

    /**
     * @return RequestContract
     * @throws PropertyNotInit
     */
    private function _entity(): RequestContract
    {
        if (!$this->entity instanceof RequestContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param RequestContract $entity
     *
     * @return RequestService
     */
    private function setEntity(RequestContract $entity): RequestService
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): RequestReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function create(UserReadonlyContract $user, array $data): RequestServiceContract
    {
        $link = Arr::get($data, 'link');

        if (strpos($link, self::LINK_PLACEHOLDER) === false) {
            throw new RuntimeException('Link should contains ' . self::LINK_PLACEHOLDER . ' placeholder');
        }

        $this->setEntity($this->make($user));
        $this->_team()->addRequest($this->_entity());

        $link = str_replace(self::LINK_PLACEHOLDER, $this->readonly()->identity()->toString(), $link);

        if (!is_string($link)) {
            Log::error('Str replace return array for link.');

            throw new RuntimeException('Something went wrong');
        }

        app()->make(EventRegistry::class)->register(new Created($this->_team(), $this->readonly(), $link));

        return $this;
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return RequestContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function make(UserReadonlyContract $user): RequestContract
    {
        return app()->make(
            RequestContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'team' => $this->_team(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function apply(): RequestServiceContract
    {
        if (!$this->_entity()->user()->identity()->equals($this->authService__()->user()->identity())) {
            throw new RuntimeException('Only invitee can apply the request.');
        }

        $this->_team()->addMember($this->_entity()->user());

        app()->make(EventRegistry::class)->register(new Applied($this->_team(), $this->readonly()));

        $this->remove();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reject(): RequestServiceContract
    {
        if (!$this->_entity()->user()->identity()->equals($this->authService__()->user()->identity())) {
            throw new RuntimeException('Only invitee can reject the request.');
        }

        app()->make(EventRegistry::class)->register(new Rejected($this->_team(), $this->readonly()));

        $this->remove();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): RequestServiceContract
    {
        $this->_team()->removeRequest($this->_entity());
        $this->entity = null;

        return $this;
    }

    /**
     * @return TeamContract
     * @throws PropertyNotInit
     */
    private function _team(): TeamContract
    {
        if (!$this->team instanceof TeamContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->team;
    }

    /**
     * @inheritDoc
     */
    public function dto(): RequestDTO
    {
        $this->populateLinkParameters();

        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function populateLinkParameters(): void
    {
        $this->linkParameters__()->put(
            collect(
                [
                    'teamId' => $this->_team()->identity()->toString(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (RequestReadonlyContract $request) {
                return $this->_mutator()->toDTO($request);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->populateLinkParameters();

        return $this->_team()->requests();
    }
}
