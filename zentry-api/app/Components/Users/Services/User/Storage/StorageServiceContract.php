<?php

namespace App\Components\Users\Services\User\Storage;

use App\Assistants\QR\Contracts\PayloadProvider;
use App\Components\Users\User\Storage\StorageDTO;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Convention\DTO\Mutators\SimplifiedDTOContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\CountableContract;
use App\Convention\Services\Contracts\FilterableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface StorageServiceContract
 *
 * @package App\Components\Users\Services\User\Storage
 */
interface StorageServiceContract
{
    /**
     * @param string $id
     *
     * @return StorageServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|PropertyNotInit
     */
    public function workWith(string $id): StorageServiceContract;

    /**
     * @param string $driver
     *
     * @return StorageServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|PropertyNotInit
     */
    public function workWithDriver(string $driver): StorageServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return StorageReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): StorageReadonlyContract;

    /**
     * @return StorageDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): StorageDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function listRO(): Collection;

    /**
     * @param array $data
     *
     * @return StorageServiceContract
     * @throws PropertyNotInit|BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException
     */
    public function change(array $data): StorageServiceContract;

    /**
     * @param array $data
     *
     * @return StorageServiceContract
     * @throws BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException|PropertyNotInit|RuntimeException
     */
    public function create(array $data): StorageServiceContract;

    /**
     * @return StorageServiceContract
     * @throws PropertyNotInit|BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException
     */
    public function remove(): StorageServiceContract;

    /**
     * @return Collection
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function drivers(): Collection;

    /**
     * @param string|null $path
     *
     * @return StorageServiceContract
     * @return StorageServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function sync(string $path = null): StorageServiceContract;
}
