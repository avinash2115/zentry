<?php

namespace App\Components\Users\Team;

use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Team\Request\RequestContract;
use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\Team\School\SchoolContract;
use App\Components\CRM\Source\TeamSourceEntity;
use App\Components\CRM\Source\Traits\HasSourceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class TeamEntity
 *
 * @package App\Components\Users\Team
 */
class TeamEntity implements TeamContract
{
    use TimestampableTrait;
    use CollectibleTrait;
    use IdentifiableTrait;
    use HasSourceTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $owner;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $members;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $requests;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $participants;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $schools;

    /**
     * TeamEntity constructor.
     *
     * @param UserReadonlyContract $owner
     * @param Identity             $identity
     * @param string               $name
     * @param string|null          $description
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        UserReadonlyContract $owner,
        Identity $identity,
        string $name,
        string $description = null
    ) {
        $this->setIdentity($identity);
        $this->setName($name);
        $this->setDescription($description);

        $this->members = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->participants = new ArrayCollection();
        $this->schools = new ArrayCollection();
        $this->setSources();

        $this->setOwner($owner);

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return TeamSourceEntity::class;
    }

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_TEAM;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function changeName(string $name): TeamContract
    {
        return $this->setName($name);
    }

    /**
     * @param string $name
     *
     * @return TeamEntity
     * @throws InvalidArgumentException
     */
    private function setName(string $name): TeamEntity
    {
        if (strEmpty($name)) {
            throw new InvalidArgumentException("Name can't be empty");
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function description(): ?string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function changeDescription(string $description = null): TeamContract
    {
        return $this->setDescription($description);
    }

    /**
     * @param string|null $description
     *
     * @return TeamEntity
     */
    private function setDescription(?string $description): TeamEntity
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function requests(): Collection
    {
        return $this->doctrineCollectionToCollection($this->requests);
    }

    /**
     * @inheritDoc
     */
    public function addRequest(RequestContract $request): TeamContract
    {
        $memberExisted = $this->members()->first(
            function (UserReadonlyContract $user) use ($request) {
                return $user->identity()->equals($request->user()->identity());
            }
        );

        $requestExisted = $this->requests()->first(
            function (RequestReadonlyContract $existedRequest) use ($request) {
                return $existedRequest->user()->identity()->equals($request->user()->identity());
            }
        );

        if (!$memberExisted instanceof UserReadonlyContract && !$requestExisted instanceof RequestReadonlyContract) {
            $this->requests->set($request->identity()->toString(), $request);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function requestByIdentity(Identity $identity): RequestContract
    {
        $entity = $this->requests()->get($identity->toString());

        if (!$entity instanceof RequestContract) {
            throw new NotFoundException('Request not found');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeRequest(RequestContract $request): TeamContract
    {
        $existed = $this->requestByIdentity($request->identity());
        $this->requests->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function members(): Collection
    {
        return $this->doctrineCollectionToCollection($this->members);
    }

    /**
     * @inheritDoc
     */
    public function addMember(UserReadonlyContract $member): TeamContract
    {
        if (!$this->members()->has($member->identity()->toString())) {
            $this->members->set($member->identity()->toString(), $member);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function memberByIdentity(Identity $identity): UserReadonlyContract
    {
        $entity = $this->members()->get($identity->toString());

        if (!$entity instanceof UserReadonlyContract) {
            throw new NotFoundException('Member not found');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeMember(UserReadonlyContract $member): TeamContract
    {
        $existed = $this->memberByIdentity($member->identity());
        $this->members->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function owner(): UserReadonlyContract
    {
        return $this->owner;
    }

    /**
     * @param UserReadonlyContract $owner
     *
     * @return TeamEntity
     */
    public function setOwner(UserReadonlyContract $owner): TeamEntity
    {
        $this->owner = $owner;

        $this->addMember($owner);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function participants(): Collection
    {
        return $this->doctrineCollectionToCollection($this->participants);
    }

    /**
     * @inheritDoc
     */
    public function addParticipant(ParticipantContract $participant): TeamContract
    {
        if (!$this->participants()->has($participant->identity()->toString())) {
            $this->participants->set($participant->identity()->toString(), $participant);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function participantByIdentity(Identity $identity): ParticipantContract
    {
        $entity = $this->participants()->get($identity->toString());

        if (!$entity instanceof ParticipantContract) {
            throw new NotFoundException('Participant not found');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeParticipant(ParticipantContract $participant): TeamContract
    {
        $existed = $this->participantByIdentity($participant->identity());
        $this->participants->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function schools(): Collection
    {
        return $this->doctrineCollectionToCollection($this->schools);
    }

    /**
     * @inheritDoc
     */
    public function addSchool(SchoolContract $entity): TeamContract
    {
        if (!$this->schools()->has($entity->identity()->toString())) {
            $this->schools->set($entity->identity()->toString(), $entity);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function schoolByIdentity(Identity $identity): SchoolContract
    {
        $entity = $this->schools()->get($identity->toString());

        if (!$entity instanceof SchoolContract) {
            throw new NotFoundException('School not found');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeSchool(SchoolContract $entity): TeamContract
    {
        $existed = $this->schoolByIdentity($entity->identity());
        $this->schools->remove($existed->identity()->toString());

        return $this;
    }
}
