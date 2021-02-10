<?php

namespace App\Components\Users\Services\User\DataProvider;

use App\Components\Users\User\DataProvider\DataProviderDTO;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Google_Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface DataProviderServiceContract
 *
 * @package App\Components\Users\Services\User\DataProvider
 */
interface DataProviderServiceContract
{
    /**
     * @param string $id
     *
     * @return DataProviderServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|PropertyNotInit
     */
    public function workWith(string $id): DataProviderServiceContract;

    /**
     * @param string $driver
     *
     * @return DataProviderServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|PropertyNotInit
     */
    public function workWithDriver(string $driver): DataProviderServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return DataProviderReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): DataProviderReadonlyContract;

    /**
     * @return DataProviderDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): DataProviderDTO;

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
     * @return DataProviderServiceContract
     *
     * @throws BindingResolutionException
     * @throws EncryptException
     * @throws Google_Exception
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws NotImplementedException
     */
    public function create(array $data): DataProviderServiceContract;

    /**
     * @return DataProviderServiceContract
     * @throws PropertyNotInit|BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException|NotFoundException
     */
    public function remove(): DataProviderServiceContract;

    /**
     * @param array $data
     *
     * @return DataProviderServiceContract
     * @throws PropertyNotInit|BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException
     */
    public function change(array $data): DataProviderServiceContract;

    /**
     * @return DataProviderServiceContract
     * @throws NotImplementedException
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws NotFoundException
     * @throws RuntimeException
     * @throws LogicException
     * @throws EncryptException
     * @throws Google_Exception
     */
    public function sync(): DataProviderServiceContract;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws NotFoundException*
     */
    public function drivers(): Collection;
}
