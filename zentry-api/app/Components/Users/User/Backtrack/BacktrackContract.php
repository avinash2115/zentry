<?php

namespace App\Components\Users\User\Backtrack;

/**
 * Interface BacktrackContract
 *
 * @package App\Components\Users\User\Backtrack
 */
interface BacktrackContract extends BacktrackReadonlyContract
{
    const DEFAULT_BACKWARD = 15;

    /**
     * @return int
     */
    public function backward(): int;
}
