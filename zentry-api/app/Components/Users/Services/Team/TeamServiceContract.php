<?php

namespace App\Components\Users\Services\Team;

use App\Components\Users\Services\Team\Request\RequestServiceContract;
use App\Components\Users\Services\Team\School\SchoolServiceContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamDTO;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface TeamServiceContract
 *
 * @package App\Components\Users\Services\Team
 */
interface TeamServiceContract extends FilterableContract
{
    /**
     * @return RequestServiceContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function requestService(): RequestServiceContract;

    /**
     * @return SchoolServiceContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function schoolService(): SchoolServiceContract;

    /**
     * @param string $id
     *
     * @return TeamServiceContract
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function workWith(string $id): TeamServiceContract;

    /**
     * @return TeamReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): TeamReadonlyContract;

    /**
     * @return TeamDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): TeamDTO;

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
     * @return TeamServiceContract
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function change(array $data): TeamServiceContract;

    /**
     * @param array $data
     *
     * @return TeamServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function create(array $data): TeamServiceContract;

    /**
     * @return TeamServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws BindingResolutionException
     * @throws PermissionDeniedException
     */
    public function remove(): TeamServiceContract;

    /**
     * @return TeamServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function leave(): TeamServiceContract;

    /**
     * @param UserReadonlyContract $user
     *
     * @return TeamServiceContract
     * @return TeamServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws BindingResolutionException
     * @throws RuntimeException
     */
    public function kick(UserReadonlyContract $user): TeamServiceContract;

    /**
     * @param SchoolReadonlyContract $school
     * @param TeamReadonlyContract   $team
     *
     * @return TeamServiceContract
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function moveSchoolTo(SchoolReadonlyContract $school, TeamReadonlyContract $team): TeamServiceContract;
}
