<?php

namespace App\Components\Sessions\Session;

use App\Components\Services\Service\ServiceReadonlyContract;
use App\Components\Sessions\Session\Goal\GoalContract;
use App\Components\Sessions\Session\Goal\Mutators\DTO\Traits\MutatorTrait as GoalMutatorTrait;
use App\Components\Sessions\Session\Note\NoteContract;
use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract as PoiParticipantReadonlyContract;
use App\Components\Sessions\Session\Poi\PoiContract;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Progress\ProgressContract;
use App\Components\Sessions\Session\SOAP\SOAPContract;
use App\Components\Sessions\Session\Stream\StreamContract;
use App\Components\Sessions\ValueObjects\Geo;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Participant\Traits\ParticipantableTrait;
use App\Components\CRM\Source\SessionSourceEntity;
use App\Components\CRM\Source\Traits\HasSourceTrait;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\DirtiableTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Tags;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class SessionEntity
 *
 * @package App\Components\Sessions\Session
 */
class SessionEntity implements SessionContract
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use ParticipantableTrait;
    use GoalMutatorTrait;
    use DirtiableTrait;
    use HasSourceTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var int
     */
    private int $status;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var string|null
     */
    private ?string $reference;

    /**
     * @var Geo|null
     */
    private ?Geo $geo = null;

    /**
     * @var Tags
     */
    private Tags $tags;

    /**
     * @var string | null
     */
    private ?string $thumbnail = null;

    /**
     * @var DateTime|null
     */
    private ?DateTime $startedAt = null;

    /**
     * @var DateTime|null
     */
    private ?DateTime $endedAt = null;

    /**
     * @var DateTime|null
     */
    private ?DateTime $scheduledOn = null;

    /**
     * @var DateTime|null
     */
    private ?DateTime $scheduledTo = null;

    /**
     * @var string | null
     */
    private ?string $sign = null;

    /**
     * @var array
     */
    private array $excludedGoals = [];

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @var ServiceReadonlyContract | null
     */
    private ?ServiceReadonlyContract $service = null;

    /**
     * @var SchoolReadonlyContract|null
     */
    private ?SchoolReadonlyContract $school = null;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $pois;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $streams;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $progress;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $goals;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $notes;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $soaps;

    /**
     * @param Identity               $identity
     * @param UserReadonlyContract   $user
     * @param string                 $name
     * @param Tags                   $tags
     * @param string                 $description
     * @param string  |null          $reference
     * @param string               $type
     * @param DateTime|null          $scheduledOn
     * @param DateTime|null          $scheduledTo
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        UserReadonlyContract $user,
        string $name,
        Tags $tags,
        string $description = '',
        string $reference = null,
        string $type = self::TYPE_DEFAULT,
        DateTime $scheduledOn = null,
        DateTime $scheduledTo = null
    ) {
        $this->setIdentity($identity);

        $this->setName($name)
            ->setType($type)
            ->setDescription($description)
            ->setReference($reference)
            ->setUser($user)
            ->setTags($tags)
            ->changeScheduledOn($scheduledOn)
            ->changeScheduledTo($scheduledTo);

        $this->changeSign(null);

        $this->pois = new ArrayCollection();
        $this->streams = new ArrayCollection();
        $this->progress = new ArrayCollection();
        $this->goals = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->soaps = new ArrayCollection();

        $this->setSources();
        $this->setParticipants();
        $this->setStatus(self::STATUS_NEW);
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function addPoi(PoiContract $poi): SessionContract
    {
        if ($this->pois()->has($poi->identity()->toString())) {
            throw new RuntimeException('Poi already exist');
        }

        $this->pois->set($poi->identity()->toString(), $poi);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addStream(StreamContract $stream): SessionContract
    {
        try {
            $this->streamByType($stream->type());
            throw new RuntimeException('Stream already exist');
        } catch (NotFoundException $exception) {
            $this->streams->set($stream->identity()->toString(), $stream);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addNote(NoteContract $entity): SessionContract
    {
        if ($this->isNoteAdded($entity->identity())) {
            throw new RuntimeException('Note already exist');
        }

        $this->notes->set($entity->identity()->toString(), $entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addSOAP(SOAPContract $entity): SessionContract
    {
        if ($this->isSOAPAdded($entity->identity())) {
            throw new RuntimeException('SOAP already exist');
        }

        $this->soaps->set($entity->identity()->toString(), $entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addGoal(GoalContract $entity): SessionContract
    {
        if ($this->goals()->has($entity->identity()->toString())) {
            throw new RuntimeException('Goal already exist');
        }

        $this->goals->set($entity->identity()->toString(), $entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addProgress(ProgressContract $progress): SessionContract
    {
        if ($this->progress()->has($progress->identity()->toString())) {
            throw new RuntimeException('Progress already exist');
        }

        $this->progress->set($progress->identity()->toString(), $progress);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeThumbnail(string $url): SessionContract
    {
        if ($url !== $this->thumbnail()) {
            $this->dirty();
        }

        $this->thumbnail = $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeName(string $name): SessionContract
    {
        if ($name !== $this->name()) {
            $this->dirty();
        }

        return $this->setName($name);
    }

    /**
     * @inheritDoc
     */
    public function changeType(string $type): SessionContract
    {
        if ($type !== $this->type()) {
            $this->dirty();
        }

        return $this->setType($type);
    }

    /**
     * @inheritDoc
     */
    public function changeDescription(string $description): SessionContract
    {
        if ($description !== $this->description()) {
            $this->dirty();
        }

        return $this->setDescription($description);
    }

    /**
     * @inheritDoc
     */
    public function changeGeo(?Geo $geo): SessionContract
    {
        if (($geo instanceof Geo && $this->geo() instanceof Geo && !$geo->equals($this->geo())) || $geo !== $this->geo(
            )) {
            $this->dirty();
        }

        return $this->setGeo($geo);
    }

    /**
     * @inheritDoc
     */
    public function changeTags(Tags $tags): SessionContract
    {
        if (count($this->tags()->toArray()) !== count($tags->toArray())) {
            $this->dirty();
        }

        return $this->setTags($tags);
    }

    /**
     * @inheritDoc
     */
    public function touch(): SessionContract
    {
        $this->setUpdatedAt();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_SESSION;
    }

    /**
     * @inheritdoc
     */
    public function user(): UserReadonlyContract
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function service(): ?ServiceReadonlyContract
    {
        return $this->service;
    }

    /**
     * @inheritdoc
     */
    public function school(): ?SchoolReadonlyContract
    {
        return $this->school;
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
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function wrap(): SessionContract
    {
        return $this->setStatus(self::STATUS_WRAPPED);
    }

    /**
     * @inheritDoc
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isStatus(int $status): bool
    {
        return $this->status() === $status;
    }

    /**
     * @inheritDoc
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function thumbnail(): ?string
    {
        return $this->thumbnail;
    }

    /**
     * @inheritDoc
     */
    public function start(): SessionContract
    {
        $this->startedAt = new DateTime();
        $this->setStatus(self::STATUS_STARTED);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function startedAt(): ?DateTime
    {
        return $this->startedAt;
    }

    /**
     * @inheritDoc
     */
    public function isStarted(): bool
    {
        return $this->startedAt() instanceof DateTime && $this->status() === self::STATUS_STARTED;
    }

    /**
     * @inheritDoc
     */
    public function end(): SessionContract
    {
        if (!$this->isStarted()) {
            throw new RuntimeException('Trying to end not started session');
        }

        $this->setStatus(self::STATUS_ENDED);

        $this->endedAt = new DateTime();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function endedAt(): ?DateTime
    {
        return $this->endedAt;
    }

    /**
     * @return bool
     */
    public function isEnded(): bool
    {
        return $this->endedAt() instanceof DateTime && $this->status() === self::STATUS_ENDED;
    }

    /**
     * @inheritDoc
     */
    public function isWrapped(): bool
    {
        return $this->status() === self::STATUS_WRAPPED;
    }

    /**
     * @inheritDoc
     */
    public function scheduledOn(): ?DateTime
    {
        return $this->scheduledOn;
    }

    /**
     * @inheritDoc
     */
    public function scheduledTo(): ?DateTime
    {
        return $this->scheduledTo;
    }

    /**
     * @inheritDoc
     */
    public function isDead(): bool
    {
        return $this->updatedAt()->add(new DateInterval('PT1M')) < (new DateTime());
    }

    /**
     * @inheritDoc
     */
    public function isScheduled(): bool
    {
        return $this->scheduledOn() instanceof DateTime;
    }

    /**
     * @inheritDoc
     */
    public function changeSign(?string $value): SessionContract
    {
        $this->dirty();

        $this->sign = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeExcludedGoals(array $value): SessionContract
    {
        $this->excludedGoals = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function excludedGoals(): array
    {
        return $this->excludedGoals;
    }

    /**
     * @inheritDoc
     */
    public function sign(): ?string
    {
        return $this->sign;
    }

    /**
     * @inheritDoc
     */
    public function reference(): ?string
    {
        return $this->reference;
    }

    /**
     * @inheritDoc
     */
    public function geo(): ?Geo
    {
        return $this->geo;
    }

    /**
     * @inheritDoc
     */
    public function tags(): Tags
    {
        return $this->tags;
    }

    /**
     * @inheritDoc
     */
    public function removePoi(PoiContract $poi): SessionContract
    {
        $existed = $this->poiByIdentity($poi->identity());
        $this->pois->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function poiByIdentity(Identity $identity): PoiContract
    {
        $poi = $this->pois()->get($identity->toString());

        if (!$poi instanceof PoiContract) {
            throw new NotFoundException('Poi not found at collection of session pois');
        }

        return $poi;
    }

    /**
     * @inheritDoc
     */
    public function pois(): Collection
    {
        return $this->doctrineCollectionToCollection($this->pois);
    }

    /**
     * @inheritDoc
     */
    public function streams(): Collection
    {
        return $this->doctrineCollectionToCollection($this->streams);
    }

    /**
     * @inheritDoc
     */
    public function streamsByTypes(Collection $types): Collection
    {
        return $types->each(
            function (string $type) {
                return $this->streamByType($type);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function removeStream(StreamContract $stream): SessionContract
    {
        $this->streams->remove($this->streamByIdentity($stream->identity())->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function streamByIdentity(Identity $identity): StreamContract
    {
        $stream = $this->streams()->get($identity->toString());

        if (!$stream instanceof StreamContract) {
            throw new NotFoundException('Stream not found at collection of session streams');
        }

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function streamByType(string $type): StreamContract
    {
        $stream = $this->streams()->first(
            static function (StreamContract $stream) use ($type) {
                return $stream->isType($type);
            }
        );

        if (!$stream instanceof StreamContract) {
            throw new NotFoundException('Stream not found at collection of session streams');
        }

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function notes(): Collection
    {
        return $this->doctrineCollectionToCollection($this->notes);
    }

    /**
     * @inheritDoc
     */
    public function noteByIdentity(Identity $identity): NoteContract
    {
        $entity = $this->notes()->get($identity->toString());

        if (!$entity instanceof NoteContract) {
            throw new NotFoundException('Note not found at collection of session notes');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeNote(NoteContract $entity): SessionContract
    {
        $this->notes->remove($this->noteByIdentity($entity->identity())->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function soaps(): Collection
    {
        return $this->doctrineCollectionToCollection($this->soaps);
    }

    /**
     * @inheritDoc
     */
    public function SOAPByIdentity(Identity $identity): SOAPContract
    {
        $entity = $this->soaps()->get($identity->toString());

        if (!$entity instanceof SOAPContract) {
            throw new NotFoundException('SOAP not found at collection of session soaps');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeSOAP(SOAPContract $entity): SessionContract
    {
        $this->soaps->remove($this->SOAPByIdentity($entity->identity())->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function checkRemovalAbility(ParticipantReadonlyContract $entity): void
    {
        $used = $this->pois()->first(
                static function (PoiReadonlyContract $poi) use ($entity) {
                    return $poi->participants()->first(
                            static function (PoiParticipantReadonlyContract $existedParticipant) use ($entity) {
                                return $existedParticipant->raw()->identity()->equals($entity->identity());
                            }
                        ) instanceof PoiParticipantReadonlyContract;
                }
            ) instanceof PoiReadonlyContract;

        if ($used) {
            throw new RuntimeException(
                "Participant {$entity->email()} {$entity->firstName()} {$entity->lastName()} is using at clip. Removal is not allowed."
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function goalByIdentity(Identity $identity): GoalContract
    {
        $entity = $this->goals()->get($identity->toString());

        if (!$entity instanceof GoalContract) {
            throw new NotFoundException('Goal not found at collection of session goals');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function removeGoal(GoalContract $entity): SessionContract
    {
        $this->goals->remove($this->goalByIdentity($entity->identity())->identity()->toString());

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
    public function progressByIdentity(Identity $identity): ProgressContract
    {
        $progress = $this->progress()->get($identity->toString());

        if (!$progress instanceof ProgressContract) {
            throw new NotFoundException('Progress not found at collection of progress');
        }

        return $progress;
    }

    /**
     * @inheritDoc
     */
    public function removeProgress(ProgressContract $progress): SessionContract
    {
        $existed = $this->progressByIdentity($progress->identity());
        $this->progress->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function progress(): Collection
    {
        return $this->doctrineCollectionToCollection($this->progress);
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return SessionSourceEntity::class;
    }

    /**
     * @param string $name
     *
     * @return SessionEntity
     * @throws InvalidArgumentException
     */
    private function setName(string $name): SessionEntity
    {
        if (strEmpty($name)) {
            throw new InvalidArgumentException("Name can't be empty");
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param Identity $identity
     *
     * @return bool
     */
    private function isNoteAdded(Identity $identity): bool
    {
        return $this->notes()->has($identity->toString());
    }

    /**
     * @param Identity $identity
     *
     * @return bool
     */
    private function isSOAPAdded(Identity $identity): bool
    {
        return $this->soaps()->has($identity->toString());
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return SessionEntity
     */
    private function setUser(UserReadonlyContract $user): SessionEntity
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return SessionEntity
     * @throws InvalidArgumentException
     */
    private function setType(string $type): SessionEntity
    {
        if (!in_array($type, self::AVAILABLE_TYPES, true)) {
            throw new InvalidArgumentException("Type {$type} is not allowed");
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    private function setStatus(int $status): SessionEntity
    {
        if (!in_array($status, self::AVAILABLE_STATUSES, true)) {
            throw new InvalidArgumentException("Status {$status} is not allowed");
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return SessionEntity
     */
    private function setDescription(string $description): SessionEntity
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string|null $reference
     *
     * @return SessionEntity
     */
    private function setReference(?string $reference): SessionEntity
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @param DateTime|null $scheduledOn
     *
     * @return SessionEntity
     */
    public function changeScheduledOn(?DateTime $scheduledOn): SessionEntity
    {
        $this->scheduledOn = $scheduledOn;

        return $this;
    }

    /**
     * @param DateTime|null $scheduledTo
     *
     * @return SessionEntity
     * @throws InvalidArgumentException
     */
    public function changeScheduledTo(?DateTime $scheduledTo): SessionEntity
    {
        if ($this->isScheduled() && is_null($scheduledTo)) {
            throw new InvalidArgumentException("Scheduled to can't be empty");
        }

        if ($scheduledTo instanceof DateTime && !$this->isScheduled()) {
            $scheduledTo = null;
        }

        $this->scheduledTo = $scheduledTo;

        return $this;
    }

    /**
     * @param Geo|null $geo
     *
     * @return SessionEntity
     */
    private function setGeo(?Geo $geo): SessionEntity
    {
        $this->geo = $geo;

        return $this;
    }

    /**
     * @param Tags $tags
     *
     * @return SessionEntity
     */
    private function setTags(Tags $tags): SessionEntity
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeService(?ServiceReadonlyContract $entity): SessionContract
    {
        $this->service = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeSchool(?SchoolReadonlyContract $entity): SessionContract
    {
        $this->school = $entity;

        return $this;
    }
}
