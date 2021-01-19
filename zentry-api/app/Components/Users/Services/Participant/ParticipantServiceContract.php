<?php

namespace App\Components\Users\Services\Participant;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Components\Users\Participant\ParticipantDTO;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Share\Contracts\SharableContract;
use App\Components\Users\Services\Participant\Goal\GoalServiceContract;
use App\Components\Users\Services\Participant\IEP\IEPServiceContract;
use App\Components\Users\Services\Participant\Therapy\TherapyServiceContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface ParticipantServiceContract
 *
 * @package App\Components\Users\Services\Participant
 */
interface ParticipantServiceContract extends FilterableContract, IndexableContract
{
    /**
     * @param string $id
     *
     * @return ParticipantServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function workWith(string $id): ParticipantServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return ParticipantReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): ParticipantReadonlyContract;

    /**
     * @return ParticipantDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): ParticipantDTO;

    /**
     * @param UserReadonlyContract $user
     * @param array                $data
     * @param TeamReadonlyContract $team
     * @param SchoolReadonlyContract $school
     *
     * @return ParticipantServiceContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    public function create(UserReadonlyContract $user,  array $data, ?TeamReadonlyContract $team = null, ?SchoolReadonlyContract $school = null): ParticipantServiceContract;

    /**
     * @return ParticipantServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     * @throws PermissionDeniedException
     */
    public function remove(): ParticipantServiceContract;

    /**
     * @param array $data
     *
     * @return ParticipantServiceContract
     * @throws BindingResolutionException|PropertyNotInit|InvalidArgumentException|Exception
     */
    public function change(array $data): ParticipantServiceContract;

    /**
     * @throws BindingResolutionException
     */
    public function listRO(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function list(): Collection;

    /**
     * @param string $referenceId
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function merge(string $referenceId): void;

    /**
     * @return GoalServiceContract
     * @throws BindingResolutionException
     */
    public function goalService(): GoalServiceContract;

    /**
     * @return IEPServiceContract
     * @throws BindingResolutionException
     */
    public function IEPService(): IEPServiceContract;

    /**
     * @return TherapyServiceContract
     * @throws BindingResolutionException
     */
    public function therapyService(): TherapyServiceContract;
}
