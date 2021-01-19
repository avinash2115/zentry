<?php

namespace App\Components\Users\Participant;

use App\Components\Users\Participant\Goal\GoalContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\IEP\IEPContract;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Components\Users\Participant\Therapy\TherapyContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface ParticipantContract
 *
 * @package App\Components\Users\Participant
 */
interface ParticipantContract extends ParticipantReadonlyContract
{
    /**
     * @param string|null $email
     *
     * @return ParticipantContract
     * @throws InvalidArgumentException
     */
    public function changeEmail(string $email = null): ParticipantContract;

    /**
     * @param string $name
     *
     * @return ParticipantContract
     * @throws InvalidArgumentException
     */
    public function changeFirstName(string $name = null): ParticipantContract;

    /**
     * @param string $name
     *
     * @return ParticipantContract
     * @throws InvalidArgumentException
     */
    public function changeLastName(string $name = null): ParticipantContract;

    /**
     * @param string $phoneCode
     *
     * @return ParticipantContract
     */
    public function changePhoneCode(string $phoneCode = null): ParticipantContract;

    /**
     * @param string $phoneNumber
     *
     * @return ParticipantContract
     */
    public function changePhoneNumber(string $phoneNumber = null): ParticipantContract;

    /**
     * @param string|null $avatar
     *
     * @return ParticipantContract
     */
    public function changeAvatar(string $avatar = null): ParticipantContract;

    /**
     * @param string|null $value
     *
     * @return ParticipantContract
     * @throws InvalidArgumentException
     */
    public function changeGender(string $value = null): ParticipantContract;

    /**
     * @param DateTime|null $value
     *
     * @return ParticipantContract
     * @throws InvalidArgumentException
     */
    public function changeDob(DateTime $value = null): ParticipantContract;

    /**
     * @param string|null $value
     *
     * @return ParticipantContract
     * @throws InvalidArgumentException
     */
    public function changeParentEmail(string $value = null): ParticipantContract;

    /**
     * @param string|null $value
     *
     * @return ParticipantContract
     * @throws InvalidArgumentException
     */
    public function changeParentPhoneNumber(string $value = null): ParticipantContract;

    /**
     * @param TeamReadonlyContract $team |null
     *
     * @return ParticipantContract
     */
    public function attachTeam(TeamReadonlyContract $team = null): ParticipantContract;

    /**
     * @param SchoolReadonlyContract $school |null
     *
     * @return ParticipantContract
     */
    public function attachSchool(SchoolReadonlyContract $school = null): ParticipantContract;

    /**
     * @return TherapyContract
     */
    public function therapyWritable(): TherapyContract;

    /**
     * @param TherapyContract $value
     *
     * @return ParticipantContract
     * @throws RuntimeException
     */
    public function assignTherapy(TherapyContract $value): ParticipantContract;

    /**
     * @param GoalReadonlyContract $goal
     *
     * @return ParticipantContract
     */
    public function addGoal(GoalReadonlyContract $goal): ParticipantContract;

    /**
     * @param GoalReadonlyContract $goal
     *
     * @return ParticipantContract
     * @throws NotFoundException
     */
    public function removeGoal(GoalReadonlyContract $goal): ParticipantContract;

    /**
     * @param Identity $identity
     *
     * @return GoalContract
     * @throws NotFoundException
     */
    public function goalByIdentity(Identity $identity): GoalContract;

    /**
     * @param IEPReadonlyContract $entity
     *
     * @return ParticipantContract
     */
    public function addIEP(IEPReadonlyContract $entity): ParticipantContract;

    /**
     * @param IEPReadonlyContract $entity
     *
     * @return ParticipantContract
     * @throws NotFoundException
     */
    public function removeIEP(IEPReadonlyContract $entity): ParticipantContract;

    /**
     * @param Identity $identity
     *
     * @return IEPContract
     * @throws NotFoundException
     */
    public function IEPByIdentity(Identity $identity): IEPContract;
}
