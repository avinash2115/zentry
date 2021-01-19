<?php

namespace App\Components\Users\User\Backtrack;

use App\Convention\Entities\Contracts\ArchivableContract;
use App\Convention\Entities\Contracts\ArchivableReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface BacktrackReadonlyContract
 *
 * @package App\Components\Users\User\Backtrack
 */
interface BacktrackReadonlyContract extends IdentifiableContract, TimestampableContract
{
    /**
     * @return int
     */
    public function backward(): int;
}
