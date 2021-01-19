<?php

namespace App\Components\Users\Services\Team\School;

use App\Components\Users\Team\School\SchoolDTO;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface SchoolServiceContract
 *
 * @package App\Components\Users\Services\Team\School
 */
interface SchoolServiceContract
{
    /**
     * @param string $id
     *
     * @return SchoolServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PermissionDeniedException
     */
    public function workWith(string $id): SchoolServiceContract;

    /**
     * @return SchoolReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): SchoolReadonlyContract;

    /**
     * @return SchoolDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): SchoolDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws UnexpectedValueException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws UnexpectedValueException
     */
    public function listRO(): Collection;

    /**
     * @param array $data
     *
     * @return SchoolServiceContract
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function change(array $data): SchoolServiceContract;

    /**
     * @param array $data
     *
     * @return $this|SchoolServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PermissionDeniedException
     */
    public function create(array $data): SchoolServiceContract;

    /**
     * @return SchoolServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws BindingResolutionException
     * @throws PermissionDeniedException
     */
    public function remove(): SchoolServiceContract;
}
