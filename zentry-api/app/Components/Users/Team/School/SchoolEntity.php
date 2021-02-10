<?php

namespace App\Components\Users\Team\School;

use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Team\TeamContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\CRM\Source\SchoolSourceEntity;
use App\Components\CRM\Source\Traits\HasSourceTrait;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use \Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class SchoolEntity
 *
 * @package App\Components\Users\Team\School
 */
class SchoolEntity implements SchoolContract
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
     * @var bool
     */
    private bool $available = true;

    /**
     * @var string|null
     */
    private ?string $streetAddress = null;

    /**
     * @var string|null
     */
    private ?string $city = null;

    /**
     * @var string|null
     */
    private ?string $state = null;

    /**
     * @var string|null
     */
    private ?string $zip = null;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $participants;

    /**
     * @var TeamReadonlyContract
     */
    private TeamReadonlyContract $team;

    /**
     * SchoolEntity constructor.
     *
     * @param TeamReadonlyContract $team
     * @param Identity             $identity
     * @param string               $name
     * @param bool                 $available
     * @param string|null          $streetAddress
     * @param string|null          $city
     * @param string|null          $state
     * @param string|null          $zip
     *
     * @throws Exception
     */
    public function __construct(
        TeamReadonlyContract $team,
        Identity $identity,
        string $name,
        bool $available,
        string $streetAddress = null,
        string $city = null,
        string $state = null,
        string $zip = null
    ) {
        $this->setIdentity($identity);
        $this->setName($name);
        $this->setAvailable($available);
        $this->setStreetAddress($streetAddress);
        $this->setCity($city);
        $this->setState($state);
        $this->setZip($zip);

        $this->participants = new ArrayCollection();
        $this->setSources();

        $this->setTeam($team);

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return SchoolSourceEntity::class;
    }

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_SCHOOL;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return SchoolContract
     * @throws InvalidArgumentException
     */
    private function setName(string $name): SchoolContract
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
    public function changeName(string $name): SchoolContract
    {
        return $this->setName($name);
    }

    /**
     * @inheritDoc
     */
    public function available(): bool
    {
        return $this->available;
    }

    /**
     * @param bool $available
     *
     * @return $this
     */
    private function setAvailable(bool $available): SchoolContract
    {
        $this->available = $available;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeAvailable(bool $available): SchoolContract
    {
        return $this->setAvailable($available);
    }

    /**
     * @inheritDoc
     */
    public function streetAddress(): ?string
    {
        return $this->streetAddress;
    }

    /**
     * @param string|null $streetAddress
     *
     * @return SchoolContract
     */
    private function setStreetAddress(?string $streetAddress): SchoolContract
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeStreetAddress(?string $streetAddress): SchoolContract
    {
        return $this->setStreetAddress($streetAddress);
    }

    /**
     * @inheritDoc
     */
    public function city(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     *
     * @return SchoolContract
     */
    private function setCity(?string $city): SchoolContract
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeCity(?string $city): SchoolContract
    {
        return $this->setCity($city);
    }

    /**
     * @inheritDoc
     */
    public function state(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     *
     * @return SchoolContract
     */
    private function setState(?string $state): SchoolContract
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeState(?string $state): SchoolContract
    {
        return $this->setState($state);
    }

    /**
     * @inheritDoc
     */
    public function zip(): ?string
    {
        return $this->zip;
    }

    /**
     * @param string|null $zip
     *
     * @return SchoolContract
     */
    private function setZip(?string $zip): SchoolContract
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeZip(?string $zip): SchoolContract
    {
        return $this->setZip($zip);
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
    public function addParticipant(ParticipantReadonlyContract $participant): SchoolContract
    {
        if (!$this->participants()->has($participant->identity()->toString())) {
            $this->participants->set($participant->identity()->toString(), $participant);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function participantByIdentity(Identity $identity): ParticipantReadonlyContract
    {
        $entity = $this->participants()->get($identity->toString());

        if (!$entity instanceof ParticipantReadonlyContract) {
            throw new NotFoundException('Participant not found');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeParticipant(ParticipantReadonlyContract $participant): SchoolContract
    {
        $existed = $this->participantByIdentity($participant->identity());
        $this->participants->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function moveTo(TeamContract $team): SchoolContract
    {
        return $this->setTeam($team);
    }

    /**
     * @param TeamReadonlyContract $team
     *
     * @return SchoolContract
     */
    private function setTeam(TeamReadonlyContract $team): SchoolContract
    {
        $this->team = $team;

        return $this;
    }
}
