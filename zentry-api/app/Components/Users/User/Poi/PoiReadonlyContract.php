<?php

namespace App\Components\Users\User\Poi;

use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface PoiReadonlyContract
 *
 * @package App\Components\Users\User\Poi
 */
interface PoiReadonlyContract extends IdentifiableContract, TimestampableContract
{
    /**
     * @return int
     */
    public function backward(): int;

    /**
     * @return int
     */
    public function forward(): int;

    /**
     * @return int
     */
    public function duration(): int;
}
