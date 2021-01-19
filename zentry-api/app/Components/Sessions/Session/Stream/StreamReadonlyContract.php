<?php

namespace App\Components\Sessions\Session\Stream;

use App\Convention\Entities\Contracts\HasCreatedAt;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use DateTime;

/**
 * Interface StreamReadonlyContract
 *
 * @package App\Components\Sessions\Session\Stream
 */
interface StreamReadonlyContract extends IdentifiableContract, HasCreatedAt
{
    public const PLAY_TOKEN_TTL = 600; //in seconds

    public const AUDIO_TYPE = 'audio';

    public const COMBINED_TYPE = 'combined';

    public const AVAILABLE_TYPES = [
        self::AUDIO_TYPE,
        self::COMBINED_TYPE,
    ];

    public const TYPES_LABELS = [
        self::AUDIO_TYPE => self::AUDIO_TYPE,
        self::COMBINED_TYPE => 'video'
    ];

    /**
     * @return string
     */
    public function type(): string;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isType(string $type): bool;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function url(): string;

    /**
     * @return int
     * @throws NotImplementedException
     */
    public function convertProgress(): int;

    /**
     * @return bool
     * @throws NotImplementedException
     */
    public function isConverted(): bool;
}
