<?php

namespace App\Components\Services\Services;

use App\Components\Services\Service\Mutators\DTO\Mutator;
use App\Components\Services\Service\Repository\ServiceRepositoryContract;
use App\Components\Services\Service\ServiceContract;
use App\Components\Services\Service\ServiceDTO;
use App\Components\Services\Service\ServiceReadonlyContract;
use App\Components\Share\Shared\SharedReadonlyContract;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\Services\Traits\GuardedTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Cache;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class ServiceService
 *
 * @package App\Components\Services\Services
 */
class ServiceService implements ServiceServiceContract
{
    use GuardedTrait;
    use FilterableTrait;
    use UserServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var ServiceContract | null
     */
    private ?ServiceContract $entity = null;

    /**
     * @var ServiceRepositoryContract | null
     */
    private ?ServiceRepositoryContract $repository = null;

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
     * @return ServiceRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): ServiceRepositoryContract
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
        if (!$this->repository instanceof ServiceRepositoryContract) {
            $this->repository = app()->make(ServiceRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): ServiceServiceContract
    {
        $this->applyFilters([]);

        $this->setEntity($this->_repository()->byIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @return ServiceContract
     * @throws PropertyNotInit
     */
    private function _entity(): ServiceContract
    {
        if (!$this->entity instanceof ServiceContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param ServiceContract $entity
     *
     * @return ServiceServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws PermissionDeniedException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    private function setEntity(ServiceContract $entity): ServiceServiceContract
    {
        $this->entity = $entity;

        $this->guardEntity(
            $this,
            function (UserReadonlyContract $user) {
                return $this->_entity()->user()->identity()->equals($user->identity());
            }
        );

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
    public function readonly(): ServiceReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): ServiceDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (ServiceReadonlyContract $entity) {
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
                $this->_repository()->filterByUsersIds([$user->identity()]);
            },
            function (SharedReadonlyContract $shared) {
            }
        );

        $this->handleFilters($this->filters());
        $this->applyFilters([]);

        return $this->_repository()->getAll();
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): ServiceServiceContract
    {
        if (Arr::has($data, 'name')) {
            $this->_entity()->changeName(Arr::get($data, 'name', ''));
        }

        if (Arr::has($data, 'code')) {
            $this->_entity()->changeCode(Arr::get($data, 'code', ''));
        }

        if (Arr::has($data, 'category')) {
            $this->_entity()->changeCategory(Arr::get($data, 'category', ''));
        }

        if (Arr::has($data, 'status')) {
            $this->_entity()->changeStatus(Arr::get($data, 'status', ''));
        }

        if (Arr::has($data, 'actions')) {
            $this->_entity()->changeActions(Arr::get($data, 'actions', ''));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(UserReadonlyContract $user, array $data): ServiceServiceContract
    {
        $entity = $this->make($user, $data);

        $this->setEntity($entity);

        $this->_repository()->persist($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): ServiceServiceContract
    {
        $this->_repository()->destroy($this->_entity());

        return $this;
    }

    /**
     * @param array $filters
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws Exception
     */
    private function handleFilters(array $filters): void
    {
        if (Arr::has($filters, 'users')) {
            $needleScopes = Arr::get($filters, 'users.collection', []);
            $isContains = filter_var(Arr::get($filters, 'users.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByUsersIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'limit')) {
            $this->_repository()->setMaxResults((int)Arr::get($filters, 'limit'));
        }
    }

    /**
     * @param UserReadonlyContract $user
     * @param array                $data
     *
     * @return ServiceContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function make(UserReadonlyContract $user, array $data): ServiceContract
    {
        return app()->make(
            ServiceContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'name' => Arr::get($data, 'name', ''),
                'code' => Arr::get($data, 'code', ''),
                'category' => Arr::get($data, 'category', ''),
                'status' => Arr::get($data, 'status', ''),
                'actions' => Arr::get($data, 'actions', ''),

            ]
        );
    }
}
