<?php

namespace App\Components\Users\Services\Participant\Goal;

use App\Components\Users\Participant\Goal\GoalDTO;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Services\Participant\Goal\Tracker\TrackerServiceContract;
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
 * Interface GoalServiceContract
 *
 * @package App\Components\Users\Services\Participant\Goal
 */
interface GoalServiceContract
{
    /**
     * @param string $id
     *
     * @return GoalServiceContract
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function workWith(string $id): GoalServiceContract;

    /**
     * @return GoalReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): GoalReadonlyContract;

    /**
     * @return GoalDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): GoalDTO;

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
     * @return GoalServiceContract
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function change(array $data): GoalServiceContract;

    /**
     * @param array $data
     *
     * @return GoalServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function create(array $data): GoalServiceContract;

    /**
     * @return GoalServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws BindingResolutionException
     * @throws PermissionDeniedException
     */
    public function remove(): GoalServiceContract;

    /**
     * @return TrackerServiceContract
     * @throws BindingResolutionException
     */
    public function trackerService(): TrackerServiceContract;
}
