<?php

namespace App\Components\Services\Services;

use App\Components\Services\Service\ServiceDTO;
use App\Components\Services\Service\ServiceReadonlyContract;
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
 * Interface ServiceServiceContract
 *
 * @package App\Components\Services\Services\Session
 */
interface ServiceServiceContract extends FilterableContract
{
    /**
     * @param string $id
     *
     * @return ServiceServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|NotFoundException|UnexpectedValueException
     */
    public function workWith(string $id): ServiceServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return ServiceReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): ServiceReadonlyContract;

    /**
     * @return ServiceDTO
     * @throws BindingResolutionException|NotFoundException|UnexpectedValueException|InvalidArgumentException|PropertyNotInit|RuntimeException
     */
    public function dto(): ServiceDTO;

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
     * @return ServiceServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException
     */
    public function create(UserReadonlyContract $user, array $data): ServiceServiceContract;

    /**
     * @param array $data
     *
     * @return ServiceServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function change(array $data): ServiceServiceContract;

    /**
     * @return ServiceServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function remove(): ServiceServiceContract;
}
