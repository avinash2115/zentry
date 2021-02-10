<?php

namespace App\Components\Sessions\Services\Goal;

use App\Components\Sessions\Session\Goal\GoalDTO;
use App\Components\Sessions\Session\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract as ParticipantGoalReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface GoalServiceContract
 *
 * @package App\Components\Sessions\Services\Goal
 */
interface GoalServiceContract
{
    /**
     * @param string $id
     *
     * @return GoalServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|NotFoundException|UnexpectedValueException
     */
    public function workWith(string $id): GoalServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return GoalReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): GoalReadonlyContract;

    /**
     * @return GoalDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): GoalDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function list(): Collection;

    /**
     * @return Collection
     */
    public function listRO(): Collection;

    /**
     * @param ParticipantReadonlyContract $participant
     * @param ParticipantGoalReadonlyContract $goal
     * @param DateTime|null $createdAt
     *
     * @return GoalServiceContract
     * @throws BindingResolutionException
     */
    public function create(
        ParticipantReadonlyContract $participant,
        ParticipantGoalReadonlyContract $goal,
        DateTime $createdAt = null
    ): GoalServiceContract;

    /**
     * @return GoalServiceContract
     * @throws BindingResolutionException|PropertyNotInit|NotFoundException
     */
    public function remove(): GoalServiceContract;
}
