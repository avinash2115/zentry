<?php

namespace App\Components\CRM\SyncLog;

use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Convention\Entities\Contracts\HasCreatedAt;
use App\Convention\Entities\Contracts\IdentifiableContract;

/**
 * Interface SyncLogReadonlyContract
 *
 * @package App\Components\CRM\SyncLog
 */
interface SyncLogReadonlyContract extends IdentifiableContract, HasCreatedAt
{
    /**
     * @return CRMReadonlyContract
     */
    public function crm(): CRMReadonlyContract;

    /**
     * @return string
     */
    public function type(): string;
}
