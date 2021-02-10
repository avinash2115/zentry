<?php

namespace App\Components\Sessions\Session\Poi;

use App\Components\Users\Participant\Contracts\AudiencableReadonlyContract;
use App\Convention\Entities\Contracts\HasCreatedAt;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\ValueObjects\Tags;
use DateTime;

/**
 * Interface PoiReadonlyContract
 *
 * @package App\Components\Sessions\Session\Poi
 */
interface PoiReadonlyContract extends IdentifiableContract, HasCreatedAt, AudiencableReadonlyContract
{
    public const POI_TYPE = 'poi';

    public const STOPWATCH_TYPE = 'stopwatch';

    public const BACKTRACK_TYPE = 'backtrack';

    public const AVAILABLE_TYPES = [
        self::POI_TYPE,
        self::STOPWATCH_TYPE,
        self::BACKTRACK_TYPE,
    ];

    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return string|null
     */
    public function name(): ?string;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isType(string $type): bool;

    /**
     * @return DateTime
     */
    public function startedAt(): DateTime;

    /**
     * @return DateTime
     */
    public function endedAt(): DateTime;

    /**
     * @return int
     */
    public function duration(): int;

    /**
     * @return Tags
     */
    public function tags(): Tags;

    /**
     * @return string|null
     */
    public function thumbnail(): ?string;

    /**
     * @return string|null
     */
    public function stream(): ?string;

    /**
     * @return bool
     */
    public function isConverted(): bool;
}
