<?php

namespace App\Components\Users\Participant\Goal;

use App\Components\Users\Participant\Goal\Tracker\TrackerContract;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\CRM\Source\ParticipantGoalSourceEntity;
use App\Components\CRM\Source\Traits\HasSourceTrait;
use App\Components\Users\ValueObjects\Participant\Goal\Meta;
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
 * Class GoalEntity
 *
 * @package App\Components\Users\Participant\Goal
 */
class GoalEntity implements GoalContract
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use HasSourceTrait;
    use CollectibleTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var Meta
     */
    private Meta $meta;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var bool
     */
    private bool $reached;

    /**
     * @var ParticipantContract
     */
    private ParticipantContract $participant;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $trackers;

    /**
     * @var IEPReadonlyContract|null
     */
    private ?IEPReadonlyContract $iep = null;

    /**
     * GoalEntity constructor.
     *
     * @param Identity            $identity
     * @param ParticipantContract $participant
     * @param string              $name
     * @param Meta                $meta
     * @param string              $description
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function __construct(
        Identity $identity,
        ParticipantContract $participant,
        string $name,
        Meta $meta,
        string $description = ''
    ) {
        $this->setIdentity($identity);
        $this->setName($name);
        $this->unReach();
        $this->setMeta($meta);
        $this->setDescription($description);

        $this->setSources();

        $this->participant = $participant;
        $this->trackers = new ArrayCollection();

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return ParticipantGoalSourceEntity::class;
    }

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_PARTICIPANT_GOAL;
    }

    /**
     * @inheritDoc
     */
    public function changeDescription(string $description): GoalContract
    {
        return $this->setDescription($description);
    }

    /**
     * @inheritDoc
     */
    public function changeName(string $name): GoalContract
    {
        return $this->setName($name);
    }

    /**
     * @inheritDoc
     */
    public function reach(): GoalContract
    {
        return $this->setReached(true);
    }

    /**
     * @inheritDoc
     */
    public function unReach(): GoalContract
    {
        return $this->setReached(false);
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
    public function isReached(): bool
    {
        return $this->reached;
    }

    /**
     * @inheritDoc
     */
    public function meta(): Meta
    {
        return $this->meta;
    }

    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @param bool $reached
     *
     * @return GoalEntity
     */
    private function setReached(bool $reached): GoalEntity
    {
        $this->reached = $reached;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return GoalEntity
     */
    private function setName(string $name): GoalEntity
    {
        if (strEmpty($name)) {
            throw new InvalidArgumentException("Name can't be empty");
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param Meta $meta
     *
     * @return GoalEntity
     */
    private function setMeta(Meta $meta): GoalEntity
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return GoalEntity
     */
    private function setDescription(string $description): GoalEntity
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function trackers(): Collection
    {
        return $this->doctrineCollectionToCollection($this->trackers);
    }

    /**
     * @inheritDoc
     */
    public function addTracker(TrackerContract $tracker): GoalContract
    {
        try {
            $this->trackerByIdentity($tracker->identity());
        } catch (NotFoundException $exception) {
            if (!$this->trackers()->some(
                static function (TrackerContract $existed) use ($tracker) {
                    return $existed->name() === $tracker->name();
                }
            )) {
                $this->trackers->set($tracker->identity()->toString(), $tracker);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function trackerByIdentity(Identity $identity): TrackerContract
    {
        $entity = $this->trackers()->first(
            static function (TrackerContract $entity) use ($identity) {
                return $entity->identity()->equals($identity);
            }
        );

        if (!$entity instanceof TrackerContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeTracker(TrackerContract $tracker): GoalContract
    {
        $existed = $this->trackerByIdentity($tracker->identity());
        $this->trackers->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeIEP(?IEPReadonlyContract $value = null): GoalContract
    {
        $this->iep = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function iep(): ?IEPReadonlyContract
    {
        return $this->iep;
    }
}
