<?php

namespace App\Components\Sessions\Session\Poi;

use App\Components\Sessions\Session\Poi\Participant\ParticipantContract;
use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\HasCreatedAtTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Tags;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class PoiEntity
 *
 * @package App\Components\Sessions\Session\Poi
 */
class PoiEntity implements PoiContract
{
    use IdentifiableTrait;
    use HasCreatedAtTrait;
    use CollectibleTrait;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @var Tags
     */
    private Tags $tags;

    /**
     * @var string | null
     */
    private ?string $thumbnail = null;

    /**
     * @var string | null
     */
    private ?string $stream = null;

    /**
     * @var DateTime
     */
    private DateTime $startedAt;

    /**
     * @var DateTime
     */
    private DateTime $endedAt;

    /**
     * @var SessionContract
     */
    private SessionContract $session;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $participants;

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     * @param SessionContract      $session
     * @param string               $type
     * @param Tags                 $tags
     * @param DateTime             $startedAt
     * @param DateTime             $endedAt
     * @param string|null          $name
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        UserReadonlyContract $user,
        SessionContract $session,
        string $type,
        Tags $tags,
        DateTime $startedAt,
        DateTime $endedAt,
        string $name = null
    ) {
        $this->setIdentity($identity);
        $this->changeName($name);

        $this->setType($type)->setSession($session)->setStartedAt(toUTC($startedAt))->setEndedAt(
            toUTC($endedAt)
        )->setTags($tags);

        $this->participants = new ArrayCollection();

        if ($this->isType(PoiReadonlyContract::POI_TYPE) && $this->duration() < $user->poi()->duration()) {
            throw new RuntimeException(
                "Duration must be more or equals to {$user->poi()->duration()}"
            );
        }

        $this->setCreatedAt();
    }

    /**
     * @param SessionContract $session
     *
     * @return PoiEntity
     */
    private function setSession(SessionContract $session): PoiEntity
    {
        $this->session = $session;

        return $this;
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
    public function isType(string $type): bool
    {
        return $this->type() === $type;
    }

    /**
     * @param string $type
     *
     * @return PoiEntity
     * @throws InvalidArgumentException
     */
    private function setType(string $type): PoiEntity
    {
        if (!in_array($type, self::AVAILABLE_TYPES, true)) {
            throw new InvalidArgumentException("Type {$type} is not allowed");
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function changeName(string $name = null): PoiContract
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function start(): PoiContract
    {
        $this->startedAt = new DateTime();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function startedAt(): DateTime
    {
        return $this->startedAt;
    }

    /**
     * @param DateTime $startedAt
     *
     * @return PoiEntity
     */
    private function setStartedAt(DateTime $startedAt): PoiEntity
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function endedAt(): DateTime
    {
        return $this->endedAt;
    }

    /**
     * @param DateTime $endedAt
     *
     * @return PoiEntity
     */
    private function setEndedAt(DateTime $endedAt): PoiEntity
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function duration(): int
    {
        return ($this->endedAt()->getTimestamp() - $this->startedAt()->getTimestamp()) < 1 ? 1 : $this->endedAt()->getTimestamp() - $this->startedAt()->getTimestamp();
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
    public function changeTags(Tags $tags): PoiContract
    {
        return $this->setTags($tags);
    }

    /**
     * @param Tags $tags
     *
     * @return PoiEntity
     */
    private function setTags(Tags $tags): PoiEntity
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeThumbnail(string $url): PoiContract
    {
        $this->thumbnail = $url;

        return $this;
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
    public function changeStream(string $stream): PoiContract
    {
        $this->stream = $stream;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function stream(): ?string
    {
        return $this->stream;
    }

    /**
     * @inheritDoc
     */
    public function isConverted(): bool
    {
        return $this->thumbnail() !== null && $this->stream() !== null;
    }

    /**
     * @return Collection
     */
    public function participants(): Collection
    {
        return $this->doctrineCollectionToCollection($this->participants);
    }

    /**
     * @inheritDoc
     */
    public function addParticipant(ParticipantReadonlyContract $participant): PoiContract
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
        $participant = $this->participants()->get($identity->toString());

        if (!$participant instanceof ParticipantContract) {
            throw new NotFoundException('Participant not found');
        }

        return $participant;
    }

    /**
     * @inheritDoc
     */
    public function removeParticipant(ParticipantReadonlyContract $participant): PoiContract
    {
        $existed = $this->participantByIdentity($participant->identity());
        $this->participants->remove($existed->identity()->toString());

        return $this;
    }
}
