<?php

namespace App\Components\Users\Services\Team\School;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\CRM\Services\Traits\CRMEntityGuardTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\Team\School\Mutators\DTO\Mutator;
use App\Components\Users\Team\School\SchoolContract;
use App\Components\Users\Team\School\SchoolDTO;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\GuardedTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class SchoolService
 *
 * @package App\Components\Users\Services\Team\School
 */
class SchoolService implements SchoolServiceContract
{
    use GuardedTrait;
    use UserServiceTrait;
    use CRMEntityGuardTrait;
    use LinkParametersTrait;

    /**
     * @var TeamContract
     */
    private TeamContract $team;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var SchoolContract | null
     */
    private ?SchoolContract $entity = null;

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
    public function workWith(string $id): SchoolServiceContract
    {
        return $this->setEntity($this->_team()->schoolByIdentity(new Identity($id)));
    }

    /**
     * @return SchoolContract
     * @throws PropertyNotInit
     */
    private function _entity(): SchoolContract
    {
        if (!$this->entity instanceof SchoolContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param SchoolContract $entity
     *
     * @return SchoolServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PermissionDeniedException
     */
    private function setEntity(SchoolContract $entity): SchoolServiceContract
    {
        $this->entity = $entity;

        $this->guardEntity(
            $this,
            function (UserReadonlyContract $user) {
                return $this->_team()->owner()->identity()->equals($user->identity());
            }
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): SchoolReadonlyContract
    {
        return $this->_entity();
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
    public function dto(): SchoolDTO
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
            function (SchoolReadonlyContract $team) {
                return $this->_mutator()->toDTO($team);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->populateLinkParameters();

        return $this->_team()->schools();
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): SchoolServiceContract
    {
        if (Arr::has($data, 'name') && Arr::get($data, 'name') !== $this->_entity()->name()) {
            $this->_entity()->changeName(Arr::get($data, 'name'));
        }

        if (Arr::has($data, 'available')) {
            if (is_bool(Arr::get($data, 'available'))) {
                $available = filter_var(Arr::get($data, 'available'), FILTER_VALIDATE_BOOLEAN);
            } else {
                throw new InvalidArgumentException('Available is not a boolean');
            }
            if ($available !== $this->_entity()->available()) {
                $this->_entity()->changeAvailable($available);
            }
        }

        if (Arr::has($data, 'street_address') && Arr::get($data, 'street_address') !== $this->_entity()->streetAddress(
            )) {
            $this->_entity()->changeStreetAddress(Arr::get($data, 'street_address'));
        }

        if (Arr::has($data, 'city') && Arr::get($data, 'city') !== $this->_entity()->city()) {
            $this->_entity()->changeCity(Arr::get($data, 'city'));
        }

        if (Arr::has($data, 'state') && Arr::get($data, 'state') !== $this->_entity()->state()) {
            $this->_entity()->changeState(Arr::get($data, 'state'));
        }

        if (Arr::has($data, 'zip') && Arr::get($data, 'zip') !== $this->_entity()->zip()) {
            $this->_entity()->changeZip(Arr::get($data, 'zip'));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): SchoolServiceContract
    {
        if (Arr::has($data, 'available')) {
            if (is_bool(Arr::get($data, 'available'))) {
                $available = filter_var(Arr::get($data, 'available'), FILTER_VALIDATE_BOOLEAN);
            } else {
                throw new InvalidArgumentException('Available is not a boolean');
            }
        } else {
            $available = true;
        }
        Arr::set($data, 'available', $available);
        $this->setEntity($this->make($data));
        $this->_team()->addSchool($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): SchoolServiceContract
    {
        $this->checkRemoving($this->readonly());
        $this->_team()->removeSchool($this->_entity());

        return $this;
    }

    /**
     * @param array                $data
     *
     * @return SchoolContract
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    private function make(array $data): SchoolContract
    {
        return app()->make(
            SchoolContract::class,
            [
                'team' => $this->_team(),
                'identity' => IdentityGenerator::next(),
                'name' => Arr::get($data, 'name', ''),
                'available' => Arr::get($data, 'available'),
                'streetAddress' => Arr::get($data, 'street_address'),
                'city' => Arr::get($data, 'city'),
                'state' => Arr::get($data, 'state'),
                'zip' => Arr::get($data, 'zip'),
            ]
        );
    }
}
