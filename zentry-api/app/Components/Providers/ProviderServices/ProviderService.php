<?php

namespace App\Components\Providers\ProviderServices;

use App\Components\Providers\ProviderService\Mutators\DTO\Mutator;
use App\Components\Providers\ProviderService\Repository\ProviderRepositoryContract;
use App\Components\Providers\ProviderService\ProviderContract;
use App\Components\Providers\ProviderService\ProviderDTO;
use App\Components\Providers\ProviderService\ProviderReadonlyContract;
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
 * Class ProviderService
 *
 * @package App\Components\Providers\Providers
 */
class ProviderService implements ProviderServiceContract
{
    use GuardedTrait;
    use FilterableTrait;
    use UserServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var ProviderContract | null
     */
    private ?ProviderContract $entity = null;

    /**
     * @var ProviderRepositoryContract | null
     */
    private ?ProviderRepositoryContract $repository = null;

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
     * @return ProviderRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): ProviderRepositoryContract
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
        if (!$this->repository instanceof ProviderRepositoryContract) {
            $this->repository = app()->make(ProviderRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): ProviderServiceContract
    {
        $this->applyFilters([]);

        $this->setEntity($this->_repository()->byIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @return ProviderContract
     * @throws PropertyNotInit
     */
    private function _entity(): ProviderContract
    {
        if (!$this->entity instanceof ProviderContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param ProviderContract $entity
     *
     * @return ProviderServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws PermissionDeniedException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    private function setEntity(ProviderContract $entity): ProviderServiceContract
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
    public function readonly(): ProviderReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): ProviderDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (ProviderReadonlyContract $entity) {
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
    public function change(array $data): ProviderServiceContract
    {
        if (Arr::has($data, 'name')) {
            $this->_entity()->changeName(Arr::get($data, 'name', ''));
        }

        if (Arr::has($data, 'code')) {
            $this->_entity()->changeCode(Arr::get($data, 'code', ''));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(UserReadonlyContract $user, array $data): ProviderServiceContract
    {
        $entity = $this->make($user, $data);

        $this->setEntity($entity);

        $this->_repository()->persist($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): ProviderServiceContract
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
     * @return ProviderContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function make(UserReadonlyContract $user, array $data): ProviderContract
    {
        return app()->make(
            ProviderContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'name' => Arr::get($data, 'name', ''),
                'code' => Arr::get($data, 'code', ''),
               
            ]
        );
    }
}
