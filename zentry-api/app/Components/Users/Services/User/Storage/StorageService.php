<?php

namespace App\Components\Users\Services\User\Storage;

use App\Assistants\Files\Drivers\Contracts\Quotable;
use App\Assistants\Files\Drivers\Kloudless\Extender;
use App\Assistants\Files\Drivers\ValueObjects\Quota;
use App\Assistants\Files\Services\Traits\CloudFileServiceTrait;
use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Components\Users\Jobs\Storage\Usage\Recalculate;
use App\Components\Users\User\Storage\Mutators\DTO\Mutator;
use App\Components\Users\User\Storage\StorageContract;
use App\Components\Users\User\Storage\StorageDTO;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\UserContract;
use App\Convention\ValueObjects\Config\Config;
use App\Components\Users\ValueObjects\Storage\Driver;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Flusher;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class StorageService
 *
 * @package App\Components\Users\Services\User\Storage
 */
class StorageService implements StorageServiceContract
{
    use CloudFileServiceTrait;
    use FileServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var StorageContract|null
     */
    private ?StorageContract $entity = null;

    /**
     * @var UserContract
     */
    private UserContract $user;

    /**
     * StorageService constructor.
     *
     * @param UserContract $user
     */
    public function __construct(UserContract $user)
    {
        $this->user = $user;
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
    public function workWith(string $id): StorageServiceContract
    {
        return $this->setEntity($this->_user()->storageByIdentity(new Identity($id)));
    }

    /**
     * @inheritDoc
     */
    public function workWithDriver(string $driver): StorageServiceContract
    {
        return $this->setEntity($this->_user()->storageByDriver($driver));
    }

    /**
     * @return StorageContract
     * @throws PropertyNotInit
     */
    private function _entity(): StorageContract
    {
        if (!$this->entity instanceof StorageContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param StorageContract $entity
     *
     * @return StorageService
     */
    private function setEntity(StorageContract $entity): StorageServiceContract
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
    public function readonly(): StorageReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): StorageDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (StorageReadonlyContract $storage) {
                return $this->_mutator()->toDTO($storage);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_user()->storages();
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): StorageServiceContract
    {
        if (Arr::has($data, 'enabled')) {
            $enabled = filter_var(Arr::get($data, 'enabled'), FILTER_VALIDATE_BOOLEAN);

            if ($enabled && !$this->_entity()->enabled()) {
                $this->_user()->storages()->each(
                    static function (StorageContract $storage) {
                        return $storage->disable();
                    }
                );

                $this->_entity()->enable();

                dispatch(new Recalculate($this->_user()->identity()));
            }
        }

        if (Arr::has($data, 'used') && Arr::get($data, 'used') !== $this->_entity()->used()) {
            $this->_entity()->changeUsed(Arr::get($data, 'used'));
        }

        if (Arr::has($data, 'capacity') && Arr::get($data, 'capacity') !== $this->_entity()->capacity()) {
            $this->_entity()->changeCapacity(Arr::get($data, 'capacity'));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): StorageServiceContract
    {
        $entity = $this->make($data);

        $this->setEntity($entity);

        $this->_user()->addStorage($entity);

        if ($entity->isDriver(StorageReadonlyContract::DRIVER_DEFAULT)) {
            $entity->changeName(
                str_replace(
                    StorageReadonlyContract::LABEL_PLACEHOLDER_APP_NAME,
                    env('APP_NAME', 'ZENTRY'),
                    $entity->name()
                )
            );
        }

        if (!app()->runningInConsole()) {
            $this->sync();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): StorageServiceContract
    {
        if ($this->_entity()->isDriver(StorageReadonlyContract::DRIVER_DEFAULT)) {
            throw new RuntimeException('Default storage cannot be removed');
        }

        if ($this->_entity()->enabled()) {
            throw new RuntimeException('Active storage cannot be removed, please enable different one first');
        }

        $this->_user()->removeStorage($this->_entity());

        $this->entity = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function drivers(): Collection
    {
        return collect(config('users.storages.drivers'))->map(
            function (array $values) {
                return new Driver(...array_values($values));
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function sync(string $path = null): StorageServiceContract
    {
        if (!$this->_entity()->isDriver(StorageReadonlyContract::DRIVER_DEFAULT)) {
            Extender::extend($this->readonly()->driver(), $this->readonly()->config());
            $this->setCloudFileService__();

            $adapter = $this->cloudFileService__()->adapter();
        } else {
            $adapter = $this->fileService__()->adapter();

            if ($path === null) {
                $path = $this->_user()->fileNamespace();
            }
        }

        if ($adapter instanceof Quotable) {
            $quota = $adapter->quota($path);
            $this->change(
                [
                    'used' => $quota->used(),
                    'capacity' => $quota->capacity(),
                ]
            );
        }

        return $this;
    }

    /**
     * @param array $data
     *
     * @return StorageContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    private function make(array $data): StorageContract
    {
        $driver = Arr::get($data, 'driver', '');

        return app()->make(
            StorageContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->_user(),
                'driver' => $driver,
                'name' => Arr::get(StorageReadonlyContract::AVAILABLE_DRIVERS, $driver, ''),
                'config' => $this->makeConfig(
                    $driver,
                    collect(Arr::get($data, 'config', []))->map(
                        function (string $value, string $attribute) {
                            return [
                                'type' => $attribute,
                                'value' => $value,
                            ];
                        }
                    )->values()->toArray()
                ),
            ]
        );
    }

    /**
     * @param string $driver
     * @param array  $options
     *
     * @return Config
     */
    private function makeConfig(string $driver, array $options): Config
    {
        return new Config($options);
    }

    /**
     * @return UserContract
     * @throws PropertyNotInit
     */
    private function _user(): UserContract
    {
        if (!$this->user instanceof UserContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->user;
    }
}
