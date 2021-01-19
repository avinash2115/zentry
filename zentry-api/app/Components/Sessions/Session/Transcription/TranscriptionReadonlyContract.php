<?php

namespace App\Components\Sessions\Session\Transcription;

use App\Convention\Entities\Contracts\HasCreatedAt;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Interface TranscriptionReadonlyContract
 *
 * @package App\Components\Sessions\Session\Transcription
 */
interface TranscriptionReadonlyContract extends IdentifiableContract, HasCreatedAt
{
    /**
     * @return Identity
     */
    public function userIdentity(): Identity;

    /**
     * @return Identity
     */
    public function sessionIdentity(): Identity;

    /**
     * @return Identity|null
     */
    public function poiIdentity(): ?Identity;

    /**
     * @return string
     */
    public function word(): string;

    /**
     * @return float
     */
    public function startedAt(): float;

    /**
     * @return float
     */
    public function endedAt(): float;

    /**
     * @return int
     */
    public function speakerTag(): int;
}
