<?php

namespace App\Components\Users\Services\Team\Request;

use App\Components\Users\Team\Request\RequestDTO;
use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface RequestServiceContract
 *
 * @package App\Components\Users\Services\Team\Request
 */
interface RequestServiceContract
{
    /**
     * @param string $id
     *
     * @return RequestServiceContract
     * @throws InvalidArgumentException
     * @throws NotFoundException
     */
    public function workWith(string $id): RequestServiceContract;

    /**
     * @return RequestReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): RequestReadonlyContract;

    /**
     * @return RequestDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): RequestDTO;

    /**
     * @param UserReadonlyContract $user
     * @param array                $data
     *
     * @return RequestServiceContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     * @throws RuntimeException
     */
    public function create(UserReadonlyContract $user, array $data): RequestServiceContract;

    /**
     * @return RequestServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws RuntimeException
     * @throws BindingResolutionException
     */
    public function apply(): RequestServiceContract;

    /**
     * @return RequestServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws RuntimeException
     * @throws BindingResolutionException
     */
    public function reject(): RequestServiceContract;

    /**
     * @return RequestServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     */
    public function remove(): RequestServiceContract;

    /**
     * @return Collection
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function listRO(): Collection;
}
