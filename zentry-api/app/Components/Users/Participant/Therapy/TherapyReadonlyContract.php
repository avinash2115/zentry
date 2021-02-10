<?php

namespace App\Components\Users\Participant\Therapy;

use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface TherapyReadonlyContract
 *
 * @package App\Components\Users\Participant\Therapy
 */
interface TherapyReadonlyContract extends IdentifiableContract, TimestampableContract
{
    public const FREQUENCY_DAILY = 'daily';

    public const FREQUENCY_WEEKLY = 'weekly';

    public const FREQUENCY_BIWEEKLY = 'biweekly';

    public const FREQUENCY_MONTHLY = 'monthly';

    public const FREQUENCY_TRIMESTER = 'trimester';

    public const FREQUENCIES_AVAILABLE = [
        self::FREQUENCY_DAILY,
        self::FREQUENCY_WEEKLY,
        self::FREQUENCY_BIWEEKLY,
        self::FREQUENCY_MONTHLY,
        self::FREQUENCY_TRIMESTER,
    ];

    public const ELIGIBILITY_TYPE_TODAY = 'today';

    public const ELIGIBILITY_TYPE_ONE_TIME = 'one_time';

    public const ELIGIBILITY_TYPE_ANNUAL = 'annual';

    public const ELIGIBILITY_TYPE_INELIGIBLE = 'ineligible';

    public const ELIGIBILITY_TYPE_DENIED = 'denied';

    public const ELIGIBILITIES_AVAILABLE = [
        self::ELIGIBILITY_TYPE_TODAY,
        self::ELIGIBILITY_TYPE_ONE_TIME,
        self::ELIGIBILITY_TYPE_ANNUAL,
        self::ELIGIBILITY_TYPE_DENIED,
        self::ELIGIBILITY_TYPE_INELIGIBLE,
    ];

    /**
     * @return string
     */
    public function diagnosis(): string;

    /**
     * @return string
     */
    public function frequency(): string;

    /**
     * @return string
     */
    public function eligibility(): string;

    /**
     * @return int
     */
    public function sessionsAmountPlanned(): int;

    /**
     * @return int
     */
    public function treatmentAmountPlanned(): int;

    /**
     * @return string
     */
    public function notes(): string;

    /**
     * @return string
     */
    public function privateNotes(): string;
}
