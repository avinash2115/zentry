<?php

namespace App\Components\Users\Services\User;

use App\Assistants\QR\Contracts\PayloadProvider;
use App\Components\Users\Services\User\DataProvider\DataProviderServiceContract;
use App\Components\Users\Services\User\Storage\StorageServiceContract;
use App\Components\Users\Services\User\CRM\CRMServiceContract;
use App\Components\Users\User\Profile\ProfileDTO;
use App\Components\Users\User\UserDTO;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Profile\Payload;
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

/**
 * Interface UserServiceContract
 *
 * @package App\Components\Users\Services\User
 */
interface UserServiceContract extends SimplifiedDTOContract, FilterableContract, PayloadProvider, CountableContract
{
    /**
     * @param string $id
     *
     * @return UserServiceContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function workWith(string $id): UserServiceContract;

    /**
     * @param string $email
     *
     * @return UserServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException|PropertyNotInit
     */
    public function workWithByEmail(string $email): UserServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return UserReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): UserReadonlyContract;

    /**
     * @return UserDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): UserDTO;

    /**
     * @param Credentials $credentials
     *
     * @return UserServiceContract
     * @throws BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException|PropertyNotInit
     */
    public function create(Credentials $credentials): UserServiceContract;

    /**
     * @param array $data
     *
     * @return UserServiceContract
     * @throws PropertyNotInit|BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException
     */
    public function change(array $data): UserServiceContract;

    /**
     * @return UserServiceContract
     * @throws PropertyNotInit|NotFoundException
     */
    public function archive(): UserServiceContract;

    /**
     * @return UserServiceContract
     * @throws PropertyNotInit|NotFoundException
     */
    public function restore(): UserServiceContract;

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function listRO(): Collection;

    /**
     * @param Payload $payload
     *
     * @return UserServiceContract
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    public function attachProfile(Payload $payload): UserServiceContract;

    /**
     * @return ProfileDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function profileDTO(): ProfileDTO;

    /**
     * @param Payload $payload
     *
     * @return UserServiceContract
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     */
    public function changeProfile(Payload $payload): UserServiceContract;

    /**
     * @return StorageServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function storageService(): StorageServiceContract;

    /**
     * @return DataProviderServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dataProviderService(): DataProviderServiceContract;

    /**
     * @return CRMServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function crmService(): CRMServiceContract;
}
