<?php

namespace App\Components\Users\Participant\IEP;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use DateTime;

/**
 * Interface IEPReadonlyContract
 *
 * @package App\Components\Users\Participant\IEP
 */
interface IEPReadonlyContract extends IdentifiableContract, TimestampableContract, CRMImportableContract
{
    /**
     * @return DateTime
     */
    public function dateActual(): DateTime;

    /**
     * @return DateTime
     */
    public function dateReeval(): DateTime;
}
