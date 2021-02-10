<?php

namespace App\Components\Providers\ProviderServices;

use App\Components\Providers\ProviderService\ProviderDTO;
use App\Components\Providers\ProviderService\ProviderReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface ProviderServiceContract
 *
 * @package App\Components\Services\Services\Session
 */
interface ProviderServiceContract extends FilterableContract
{
    /**
     * @param string $id
     *
     * @return ProviderServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|NotFoundException|UnexpectedValueException
     */
    public function workWith(string $id): ProviderServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return ProviderReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): ProviderReadonlyContract;

    /**
     * @return ProviderDTO
     * @throws BindingResolutionException|NotFoundException|UnexpectedValueException|InvalidArgumentException|PropertyNotInit|RuntimeException
     */
    public function dto(): ProviderDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function listRO(): Collection;

    /**
     * @param UserReadonlyContract $user
     * @param array                $data
     *
     * @return ProviderServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException
     */
    public function create(UserReadonlyContract $user, array $data): ProviderServiceContract;

    /**
     * @param array $data
     *
     * @return ProviderServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function change(array $data): ProviderServiceContract;

    /**
     * @return ProviderServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function remove(): ProviderServiceContract;
}
