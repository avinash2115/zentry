<?php

namespace App\Components\Users\Participant\Goal\Tracker;

use App\Convention\Entities\Contracts\HasCreatedAt;
use App\Convention\Entities\Contracts\IdentifiableContract;
use Illuminate\Support\Collection;

/**
 * Interface TrackerReadonlyContract
 *
 * @package App\Components\Users\Participant\Goal\Tracker
 */
interface TrackerReadonlyContract extends HasCreatedAt, IdentifiableContract
{
    public const TYPE_POSITIVE = 'positive';

    public const TYPE_NEGATIVE = 'negative';

    public const TYPE_NEUTRAL = 'neutral';

    public const TYPES_AVAILABLE = [
        self::TYPE_POSITIVE,
        self::TYPE_NEGATIVE,
        self::TYPE_NEUTRAL,
    ];

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return string
     */
    public function icon(): string;

    /**
     * @return string
     */
    public function color(): string;

    /**
     * @return Collection
     */
    public function sessions(): Collection;
}
