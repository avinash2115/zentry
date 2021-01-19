<?php

namespace App\Components\Users\Services\Device;

use App\Assistants\Events\EventRegistry;
use App\Components\Users\Device\DeviceContract;
use App\Components\Users\Device\DeviceDTO;
use App\Components\Users\Device\DeviceReadonlyContract;
use App\Components\Users\Device\Events\Broadcast\Created;
use App\Components\Users\Device\Events\Broadcast\Exists;
use App\Components\Users\Device\Events\Broadcast\Removed;
use App\Components\Users\Device\Mutators\DTO\Mutator;
use App\Components\Users\Device\Repository\DeviceRepositoryContract;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\CountableTrait;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\Services\Traits\GuardedTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class DeviceService
 *
 * @package App\Components\Users\Services\Device
 */
class DeviceService implements DeviceServiceContract
{
    use GuardedTrait;
    use FilterableTrait;
    use CountableTrait;
    use UserServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var DeviceContract | null
     */
    private ?DeviceContract $entity = null;

    /**
     * @var DeviceRepositoryContract | null
     */
    private ?DeviceRepositoryContract $repository = null;

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
     * @return self
     * @throws BindingResolutionException
     */
    private function setRepository(): self
    {
        if (!$this->repository instanceof DeviceRepositoryContract) {
            $this->repository = app()->make(DeviceRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @return DeviceRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): DeviceRepositoryContract
    {
        $this->setRepository();

        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): DeviceServiceContract
    {
        $this->applyFilters([]);

        $this->setEntity($this->_repository()->byIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWithReference(string $reference): DeviceServiceContract
    {
        $this->applyFilters([]);

        $entity = $this->_repository()->filterByReferences([$reference])->getOne();

        if (!$entity instanceof DeviceReadonlyContract) {
            throw new NotFoundException();
        }

        $this->setEntity($entity);

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
    public function readonly(): DeviceReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): DeviceDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @param DeviceContract $entity
     *
     * @return DeviceServiceContract
     */
    private function setEntity(DeviceContract $entity): DeviceServiceContract
    {
        $this->entity = $entity;

        $this->guardEntity(
            $this,
            function (UserReadonlyContract $user) {
                return $this->_entity()->user()->identity()->equals($user->identity()) || $this->_entity()->reference(
                    ) === $this->authService__()->deviceReference();
            }
        );

        return $this;
    }

    /**
     * @return DeviceContract
     * @throws PropertyNotInit
     */
    private function _entity(): DeviceContract
    {
        if (!$this->entity instanceof DeviceContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @inheritDoc
     */
    public function create(UserReadonlyContract $user, ConnectingPayload $payload): DeviceServiceContract
    {
        $entity = $this->_repository()->filterByReferences([$payload->reference()])->getOne();

        if (!$entity instanceof DeviceContract) {
            $entity = $this->make($user, $payload);

            $this->_repository()->persist($entity);

            app()->make(EventRegistry::class)->registerBroadcast(new Created($this->_mutator()->toDTO($entity)));
        } else {
            if (!$entity->user()->identity()->equals($user->identity())) {
                $entity->transfer($user);
            }

            app()->make(EventRegistry::class)->registerBroadcast(new Exists($this->_mutator()->toDTO($entity)));
        }

        $this->setEntity($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->guardRepository(
            $this,
            function (UserReadonlyContract $user) {
                $deviceReference = $this->authService__()->deviceReference();

                if (is_string($deviceReference)) {
                    $this->_repository()->filterByReferences([$deviceReference]);
                } else {
                    $this->_repository()->filterByUsersIds([$user->identity()]);
                }
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
    public function list(): Collection
    {
        return $this->listRO()->sortByDesc(
            function (DeviceReadonlyContract $device) {
                return $device->createdAt();
            }
        )->map(
            function (DeviceContract $device) {
                return $this->_mutator()->toDTO($device);
            }
        );
    }

    /**
     * @param array $filters
     *
     * @throws BindingResolutionException
     */
    private function handleFilters(array $filters): void
    {
        if (Arr::has($filters, 'users')) {
            $needleScopes = Arr::get($filters, 'users.collection', []);
            $isContains = filter_var(Arr::get($filters, 'users.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByUsersIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'references')) {
            $needleScopes = Arr::get($filters, 'references.collection', []);
            $isContains = filter_var(Arr::get($filters, 'references.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByReferences($needleScopes, $isContains);
        }
    }

    /**
     * @param UserReadonlyContract $user
     * @param ConnectingPayload    $payload
     *
     * @return DeviceContract
     * @throws BindingResolutionException
     */
    private function make(UserReadonlyContract $user, ConnectingPayload $payload): DeviceContract
    {
        return app()->make(
            DeviceContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'payload' => $payload,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function remove(): DeviceServiceContract
    {
        $entity = $this->_entity();

        $this->_repository()->destroy($entity);

        app()->make(EventRegistry::class)->registerBroadcast(new Removed($this->dto()));

        return $this;
    }
}
