<?php

namespace App\Components\Users\Services\Team;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Users\Services\Team\Request\RequestServiceContract;
use App\Components\Users\Services\Team\School\SchoolServiceContract;
use App\Components\CRM\Services\Traits\CRMEntityGuardTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\Team\Mutators\DTO\Mutator;
use App\Components\Users\Team\Repository\TeamRepositoryContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamContract;
use App\Components\Users\Team\TeamDTO;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\Services\Traits\GuardedTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use \Arr;
use Illuminate\Support\Collection;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class TeamService
 *
 * @package App\Components\Users\Services\Team
 */
class TeamService implements TeamServiceContract
{
    use SimplifiedDTOServiceTrait;
    use GuardedTrait;
    use FilterableTrait;
    use UserServiceTrait;
    use CRMEntityGuardTrait;
    use LinkParametersTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var TeamContract | null
     */
    private ?TeamContract $entity = null;

    /**
     * @var TeamRepositoryContract | null
     */
    private ?TeamRepositoryContract $repository = null;

    /**
     * @var RequestServiceContract|null
     */
    private ?RequestServiceContract $requestService = null;

    /**
     * @var SchoolServiceContract|null
     */
    private ?SchoolServiceContract $schoolService = null;

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
     * @return TeamRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): TeamRepositoryContract
    {
        $this->setRepository();

        return $this->repository;
    }

    /**
     * @return self
     * @throws BindingResolutionException
     */
    private function setRepository(): self
    {
        if (!$this->repository instanceof TeamRepositoryContract) {
            $this->repository = app()->make(TeamRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @return TeamServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setRequestService(): TeamServiceContract
    {
        $this->requestService = app()->make(
            RequestServiceContract::class,
            [
                'team' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function requestService(): RequestServiceContract
    {
        if (!$this->requestService instanceof RequestServiceContract) {
            $this->setRequestService();
        }

        return $this->requestService;
    }

    /**
     * @return TeamServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setSchoolService(): TeamServiceContract
    {
        $this->schoolService = app()->make(
            SchoolServiceContract::class,
            [
                'team' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function schoolService(): SchoolServiceContract
    {
        if (!$this->schoolService instanceof SchoolServiceContract) {
            $this->setSchoolService();
        }

        return $this->schoolService;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): TeamServiceContract
    {
        $this->applyFilters([]);

        return $this->setEntity($this->_repository()->byIdentity(new Identity($id)));
    }

    /**
     * @return TeamContract
     * @throws PropertyNotInit
     */
    private function _entity(): TeamContract
    {
        if (!$this->entity instanceof TeamContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param TeamContract $entity
     *
     * @return TeamServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    private function setEntity(TeamContract $entity): TeamServiceContract
    {
        $this->entity = $entity;

        $this->guardEntity(
            $this,
            function (UserReadonlyContract $user) {
                return $this->_entity()->owner()->identity()->equals($user->identity());
            }
        );

        $this->setRequestService();
        $this->setSchoolService();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): TeamReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): TeamDTO
    {
        $this->linkParameters__()->put(collect(['userId' => $this->_entity()->owner()->identity()->toString()]));

        $dto = $this->_mutator()->toDTO($this->_entity());

        $this->fullMutation();

        return $dto;
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (TeamReadonlyContract $entity) {
                $this->linkParameters__()->put(collect(['userId' => $entity->owner()->identity()->toString()]));

                return $this->_mutator()->toDTO($entity);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->guardRepository(
            $this,
            function (UserReadonlyContract $user) {
                $this->_repository()->filterByUserPresence($user->identity()->toString());
            },
            function () { }
        );

        $this->handleFilters($this->filters());

        $results = $this->_repository()->getAll();

        $this->applyFilters([]);

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): TeamServiceContract
    {
        if (Arr::has($data, 'name')) {
            $this->_entity()->changeName(Arr::get($data, 'name'));
        }

        if (Arr::has($data, 'description')) {
            $this->_entity()->changeDescription(Arr::get($data, 'description'));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): TeamServiceContract
    {
        $this->setEntity($this->make($data));
        $this->_repository()->persist($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): TeamServiceContract
    {
        $this->checkRemoving($this->readonly());
        $this->_repository()->destroy($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function leave(): TeamServiceContract
    {
        $user = $this->authService__()->user()->readonly();

        if ($this->_entity()->owner()->identity()->equals($user->identity())) {
            throw new RuntimeException("Owner can't leave his own team. Only Removal available");
        }

        $this->_entity()->removeMember($this->authService__()->user()->readonly());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function kick(UserReadonlyContract $user): TeamServiceContract
    {
        if (!$this->_entity()->owner()->identity()->equals($this->authService__()->user()->identity())) {
            throw new RuntimeException('Only the team owner have the ability to remove members.');
        }

        $this->_entity()->removeMember($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function moveSchoolTo(SchoolReadonlyContract $school, TeamReadonlyContract $team): TeamServiceContract
    {
        $parent = $this->_entity();

        $school = $parent->schoolByIdentity($school->identity());
        $this->_entity()->removeSchool($school);
        $this->workWith($team->identity());
        $school->moveTo($this->_entity());
        $this->_entity()->addSchool($school);

        return $this->workWith($parent->identity());
    }

    /**
     * @param array $filters
     *
     * @throws BindingResolutionException
     */
    private function handleFilters(array $filters): void
    {
        if (Arr::has($filters, 'members')) {
            $needleScopes = Arr::get($filters, 'members.collection', []);
            $isContains = filter_var(Arr::get($filters, 'members.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByMemberIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'owners')) {
            $needleScopes = Arr::get($filters, 'owners.collection', []);
            $isContains = filter_var(Arr::get($filters, 'owners.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByOwnerIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'schools')) {
            $needleScopes = Arr::get($filters, 'schools.collection', []);
            $isContains = filter_var(Arr::get($filters, 'schools.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterBySchoolIds($needleScopes, $isContains);
        }
    }

    /**
     * @param array $data
     *
     * @return TeamContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    private function make(array $data): TeamContract
    {
        return app()->make(
            TeamContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'owner' => $this->authService__()->user()->readonly(),
                'name' => Arr::get($data, 'name', ''),
                'description' => Arr::get($data, 'description'),
            ]
        );
    }
}
