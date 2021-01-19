<?php

namespace App\Components\Users\Participant\IEP;

use App\Components\Users\Participant\IEP\Tracker\TrackerContract;
use DateTime;

/**
 * Interface IEPContract
 *
 * @package App\Components\Users\Participant\IEP
 */
interface IEPContract extends IEPReadonlyContract
{
    /**
     * @param DateTime $value
     *
     * @return IEPContract
     */
    public function changeDateActual(DateTime $value): IEPContract;

    /**
     * @param DateTime $value
     *
     * @return IEPContract
     */
    public function changeDateReeval(DateTime $value): IEPContract;
}
