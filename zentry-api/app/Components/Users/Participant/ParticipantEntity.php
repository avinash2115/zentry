<?php

namespace App\Components\Users\Participant;

use App\Components\Users\Participant\Goal\GoalContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\IEP\IEPContract;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Components\Users\Participant\Therapy\TherapyContract;
use App\Components\Users\Participant\Therapy\TherapyReadonlyContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\CRM\Source\ParticipantSourceEntity;
use App\Components\CRM\Source\Traits\HasSourceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class ParticipantEntity
 *
 * @package App\Components\Users\Participant
 */
class ParticipantEntity implements ParticipantContract
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use CollectibleTrait;
    use HasSourceTrait;

    /**
     * @var string|null
     */
    private ?string $email;

    /**
     * @var string
     */
    private ?string $firstName;

    /**
     * @var string
     */
    private ?string $lastName;

    /**
     * @var string|null
     */
    private ?string $phoneCode;

    /**
     * @var string|null
     */
    private ?string $phoneNumber;

    /**
     * @var string|null
     */
    private ?string $avatar;

    /**
     * @var string|null
     */
    private ?string $gender;

    /**
     * @var DateTime|null
     */
    private ?DateTime $dob;

    /**
     * @var string|null
     */
    private ?string $parentEmail;

    /**
     * @var string|null
     */
    private ?string $parentPhoneNumber;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @var TherapyContract|null
     */
    private ?TherapyContract $therapy = null;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $goals;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $ieps;

    /**
     * @var TeamReadonlyContract|null
     */
    private ?TeamReadonlyContract $team = null;

    /**
     * @var SchoolReadonlyContract|null
     */
    private ?SchoolReadonlyContract $school = null;

    /**
     * ParticipantEntity constructor.
     *
     * @param UserReadonlyContract        $user
     * @param Identity                    $identity
     * @param TeamReadonlyContract|null   $team
     * @param SchoolReadonlyContract|null $school
     * @param string|null                 $email
     * @param string|null                 $firstName
     * @param string|null                 $lastName
     * @param string|null                 $phoneCode
     * @param string|null                 $phoneNumber
     * @param string|null                 $avatar
     * @param string|null                 $gender
     * @param DateTime|null               $dob
     * @param string|null                 $parentEmail
     * @param string|null                 $parentPhoneNumber
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        UserReadonlyContract $user,
        Identity $identity,
        ?TeamReadonlyContract $team = null,
        ?SchoolReadonlyContract $school = null,
        string $email = null,
        string $firstName = null,
        string $lastName = null,
        string $phoneCode = null,
        string $phoneNumber = null,
        string $avatar = null,
        string $gender = null,
        DateTime $dob = null,
        string $parentEmail = null,
        string $parentPhoneNumber = null
    ) {
        $this->setIdentity($identity);
        $this->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setPhoneCode($phoneCode)
            ->setPhoneNumber($phoneNumber)
            ->setAvatar($avatar);

        $this->changeGender($gender);
        $this->changeDob($dob);
        $this->changeParentEmail($parentEmail);
        $this->changeParentPhoneNumber($parentPhoneNumber);

        $this->setUser($user);
        $this->goals = new ArrayCollection();
        $this->ieps = new ArrayCollection();
        $this->setSources();

        $this->setTeam($team);
        $this->setSchool($school);
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return ParticipantSourceEntity::class;
    }

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_PARTICIPANT;
    }

    /**
     * @inheritDoc
     */
    public function email(): ?string
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function changeEmail(string $email = null): ParticipantContract
    {
        $this->setEmail($email);

        return $this;
    }

    /**
     * @param string|null $email
     *
     * @return ParticipantEntity
     * @throws InvalidArgumentException
     */
    private function setEmail(string $email = null): ParticipantEntity
    {
        if (strEmpty((string)$email) && (strEmpty((string)$this->firstName()) || strEmpty((string)$this->lastName()))) {
            throw new InvalidArgumentException('Email or first name and last name is required');
        }

        if (!strEmpty((string)$email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The email {$email} format is wrong");
        }

        $this->email = $email;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function firstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @inheritDoc
     */
    public function changeFirstName(string $firstName = null): ParticipantContract
    {
        $this->setFirstName($firstName);

        return $this;
    }

    /**
     * @param string $firstName
     *
     * @return ParticipantEntity
     */
    private function setFirstName(string $firstName = null): ParticipantEntity
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function lastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @inheritDoc
     */
    public function fullName(): string
    {
        return !strEmpty($this->firstName()) ? implode(' ', [$this->firstName(), $this->lastName()]) : $this->email();
    }

    /**
     * @inheritDoc
     */
    public function changeLastName(string $lastName = null): ParticipantContract
    {
        $this->setLastName($lastName);

        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return ParticipantEntity
     */
    private function setLastName(string $lastName = null): ParticipantEntity
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function displayName(): string
    {
        return trim("{$this->firstName()} {$this->lastName()}") . ($this->email() ? "({$this->email()})" : '');
    }

    /**
     * @inheritDoc
     */
    public function phoneCode(): ?string
    {
        return $this->phoneCode;
    }

    /**
     * @inheritDoc
     */
    public function changePhoneCode(string $phoneCode = null): ParticipantContract
    {
        $this->setPhoneCode($phoneCode);

        return $this;
    }

    /**
     * @param string|null $phoneCode
     *
     * @return ParticipantEntity
     */
    private function setPhoneCode(string $phoneCode = null): ParticipantEntity
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @inheritDoc
     */
    public function changePhoneNumber(string $phoneNumber = null): ParticipantContract
    {
        $this->setPhoneNumber($phoneNumber);

        return $this;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return ParticipantEntity
     */
    private function setPhoneNumber(string $phoneNumber = null): ParticipantEntity
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return ParticipantEntity
     */
    private function setUser(UserReadonlyContract $user): ParticipantEntity
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function user(): UserReadonlyContract
    {
        return $this->user;
    }

    /**
     * @param TeamReadonlyContract|null $team
     *
     * @return ParticipantEntity
     */
    private function setTeam(?TeamReadonlyContract $team): ParticipantEntity
    {
        if (!$team instanceof TeamReadonlyContract) {
            $this->setSchool(null);
        }

        if ($team instanceof TeamReadonlyContract && $this->team() instanceof TeamReadonlyContract && !$this->team()
                ->identity()
                ->equals($team->identity())) {
            $this->setSchool(null);
        }

        $this->team = $team;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function team(): ?TeamReadonlyContract
    {
        return $this->team;
    }

    /**
     * @inheritDoc
     */
    public function avatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @inheritDoc
     */
    public function changeAvatar(string $avatar = null): ParticipantContract
    {
        return $this->setAvatar($avatar);
    }

    /**
     * @param string|null $avatar
     *
     * @return ParticipantEntity
     */
    private function setAvatar(string $avatar = null): ParticipantEntity
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function gender(): ?string
    {
        return $this->gender;
    }

    /**
     * @inheritDoc
     */
    public function changeGender(string $value = null): ParticipantContract
    {
        if ($value !== null && !in_array($value, self::GENDERS_AVAILABLE, true)) {
            throw new InvalidArgumentException("{$value} gender is not allowed");
        }

        $this->gender = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dob(): ?DateTime
    {
        return $this->dob;
    }

    /**
     * @inheritDoc
     */
    public function parentEmail(): ?string
    {
        return $this->parentEmail;
    }

    /**
     * @inheritDoc
     */
    public function parentPhoneNumber(): ?string
    {
        return $this->parentPhoneNumber;
    }

    /**
     * @inheritDoc
     */
    public function changeDob(DateTime $value = null): ParticipantContract
    {
        if ($value instanceof DateTime && $value->getTimestamp() >= (new DateTime())->getTimestamp()) {
            throw new InvalidArgumentException("DoB cannot be greater or equals now");
        }

        $this->dob = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeParentEmail(string $value = null): ParticipantContract
    {
        $this->parentEmail = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeParentPhoneNumber(string $value = null): ParticipantContract
    {
        $this->parentPhoneNumber = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function attachTeam(TeamReadonlyContract $team = null): ParticipantContract
    {
        return $this->setTeam($team);
    }

    /**
     * @inheritDoc
     */
    public function attachSchool(SchoolReadonlyContract $school = null): ParticipantContract
    {
        return $this->setSchool($school);
    }

    /**
     * @inheritDoc
     */
    public function therapy(): TherapyReadonlyContract
    {
        if (!$this->therapy instanceof TherapyReadonlyContract) {
            $this->assignTherapy(
                app()->make(
                    TherapyContract::class,
                    [
                        'identity' => IdentityGenerator::next(),
                        'participant' => $this,
                        'diagnosis' => 'None',
                        'frequency' => TherapyReadonlyContract::FREQUENCY_DAILY,
                        'eligibility' => TherapyReadonlyContract::ELIGIBILITY_TYPE_ONE_TIME,
                        'sessionsAmountPlanned' => 0,
                        'treatmentAmountPlanned' => 0,
                    ]
                )
            );
        }

        return $this->therapy;
    }

    /**
     * @inheritDoc
     */
    public function therapyWritable(): TherapyContract
    {
        $this->therapy();

        return $this->therapy;
    }

    /**
     * @inheritDoc
     */
    public function assignTherapy(TherapyContract $value): ParticipantContract
    {
        if ($this->therapy instanceof TherapyContract) {
            throw new RuntimeException('Already assigned');
        }

        $this->therapy = $value;

        return $this;
    }

    /**
     * @param SchoolReadonlyContract|null $school
     *
     * @return ParticipantEntity
     */
    private function setSchool(?SchoolReadonlyContract $school): ParticipantEntity
    {
        if ($school instanceof SchoolReadonlyContract && !$this->team() instanceof TeamReadonlyContract) {
            throw new UnexpectedValueException("Can't add school to participant without team");
        }

        $this->school = $school;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function school(): ?SchoolReadonlyContract
    {
        return $this->school;
    }

    /**
     * @inheritDoc
     */
    public function addGoal(GoalReadonlyContract $goal): ParticipantContract
    {
        try {
            $this->goalByIdentity($goal->identity());
        } catch (NotFoundException $exception) {
            $this->goals->set($goal->identity()->toString(), $goal);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeGoal(GoalReadonlyContract $goal): ParticipantContract
    {
        $existed = $this->goalByIdentity($goal->identity());

        $this->goals->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function goals(): Collection
    {
        return $this->doctrineCollectionToCollection($this->goals);
    }

    /**
     * @inheritDoc
     */
    public function goalByIdentity(Identity $identity): GoalContract
    {
        $goal = $this->goals()->first(
            static function (GoalContract $goal) use ($identity) {
                return $goal->identity()->equals($identity);
            }
        );

        if (!$goal instanceof GoalContract) {
            throw new NotFoundException();
        }

        return $goal;
    }

    /**
     * @inheritDoc
     */
    public function addIEP(IEPReadonlyContract $entity): ParticipantContract
    {
        try {
            $this->IEPByIdentity($entity->identity());
        } catch (NotFoundException $exception) {
            $this->ieps->set($entity->identity()->toString(), $entity);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeIEP(IEPReadonlyContract $entity): ParticipantContract
    {
        $existed = $this->IEPByIdentity($entity->identity());

        $this->ieps->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function IEPByIdentity(Identity $identity): IEPContract
    {
        $entity = $this->ieps()->first(
            static function (IEPContract $goal) use ($identity) {
                return $goal->identity()->equals($identity);
            }
        );

        if (!$entity instanceof IEPContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function ieps(): Collection
    {
        return $this->doctrineCollectionToCollection($this->ieps);
    }
}
