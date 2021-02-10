<?php

namespace App\Components\Sessions\Session\SOAP;

use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface SOAPReadonlyContract
 *
 * @package App\Components\Sessions\Session\SOAP
 */
interface SOAPReadonlyContract extends IdentifiableContract, TimestampableContract
{
    public const RATE_REGRESSION = 'regression';

    public const RATE_NO_PROGRESS = 'no_progress';

    public const RATE_MINIMAL_PROGRESS = 'minimal_progress';

    public const RATE_PROGRESS = 'progress';

    public const RATE_GOAL_MET = 'goal_met';

    public const RATE_GOAL_NOT_TARGETED = 'goal_not_targeted';

    public const RATE_MAINTENANCE = 'maintenance';

    public const RATES_AVAILABLE = [
        self::RATE_REGRESSION,
        self::RATE_NO_PROGRESS,
        self::RATE_MINIMAL_PROGRESS,
        self::RATE_PROGRESS,
        self::RATE_GOAL_MET,
        self::RATE_GOAL_NOT_TARGETED,
        self::RATE_MAINTENANCE,
    ];

    /**
     * @return bool
     */
    public function isPresent();

    /**
     * @return string
     */
    public function rate();

    /**
     * @return string
     */
    public function activity();

    /**
     * @return string
     */
    public function note();

    /**
     * @return string
     */
    public function plan();

    /**
     * @return ParticipantReadonlyContract
     */
    public function participant();

    /**
     * @return GoalReadonlyContract|null
     */
    public function goal();
}
