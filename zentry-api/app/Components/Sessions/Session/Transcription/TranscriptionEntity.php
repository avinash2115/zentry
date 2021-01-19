<?php

namespace App\Components\Sessions\Session\Transcription;

use App\Convention\Entities\Traits\DirtiableTrait;
use App\Convention\Entities\Traits\HasCreatedAtTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Interface TranscriptionContract
 *
 * @package App\Components\Sessions\Session\Transcription
 */
class TranscriptionEntity implements TranscriptionContract
{
    use HasCreatedAtTrait;
    use DirtiableTrait;
    use IdentifiableTrait;

    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @var Identity
     */
    private Identity $sessionIdentity;

    /**
     * @var Identity|null
     */
    private ?Identity $poiIdentity = null;

    /**
     * @var string
     */
    private string $word;

    /**
     * @var float
     */
    private float $startedAt;

    /**
     * @var float
     */
    private float $endedAt;

    /**
     * @var int
     */
    private int $speakerTag;

    /**
     * @param Identity      $identity
     * @param Identity      $userIdentity
     * @param Identity      $sessionIdentity
     * @param string        $word
     * @param float         $startedAt
     * @param float         $endedAt
     * @param int           $speakerTag
     * @param Identity|null $poiIdentity
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        Identity $userIdentity,
        Identity $sessionIdentity,
        string $word,
        float $startedAt,
        float $endedAt,
        int $speakerTag = 0,
        ?Identity $poiIdentity = null
    ) {
        $this->setIdentity($identity);
        $this->userIdentity = $userIdentity;
        $this->sessionIdentity = $sessionIdentity;
        $this->poiIdentity = $poiIdentity;
        $this->setWord($word);
        $this->setStartedAt($startedAt);
        $this->setEndedAt($endedAt);
        $this->speakerTag = $speakerTag;

        $this->setCreatedAt();
    }

    /**
     * @inheritDoc
     */
    public function userIdentity(): Identity
    {
        return $this->userIdentity;
    }

    /**
     * @inheritDoc
     */
    public function sessionIdentity(): Identity
    {
        return $this->sessionIdentity;
    }

    /**
     * @inheritDoc
     */
    public function poiIdentity(): ?Identity
    {
        return $this->poiIdentity;
    }

    /**
     * @inheritDoc
     */
    public function word(): string
    {
        return $this->word;
    }

    /**
     * @param string $word
     *
     * @return TranscriptionEntity
     * @throws InvalidArgumentException
     */
    private function setWord(string $word): TranscriptionEntity
    {
        if (strEmpty($word)) {
            throw new InvalidArgumentException('Word can\'t be empty');
        }

        $this->word = $word;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function startedAt(): float
    {
        return $this->startedAt;
    }

    /**
     * @param float $startedAt
     *
     * @return TranscriptionEntity
     * @throws InvalidArgumentException
     */
    private function setStartedAt(float $startedAt): TranscriptionEntity
    {
        if ($startedAt < 0) {
            throw new InvalidArgumentException('Started at should be more or equals zero');
        }

        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function endedAt(): float
    {
        return $this->endedAt;
    }

    /**
     * @param float $endedAt
     *
     * @return TranscriptionEntity
     * @throws InvalidArgumentException
     */
    private function setEndedAt(float $endedAt): TranscriptionEntity
    {
        if ($endedAt < $this->startedAt()) {
            throw new InvalidArgumentException('Ended at can\'t be less or equals start time');
        }

        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function speakerTag(): int
    {
        return $this->speakerTag;
    }
}
