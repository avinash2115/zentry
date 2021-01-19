<?php

namespace App\Components\Share\Shared;

use App\Components\Share\ValueObjects\Payload;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface SharedReadonlyContract
 *
 * @package App\Components\Share\Shared
 */
interface SharedReadonlyContract extends IdentifiableContract, TimestampableContract
{
    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return Payload
     */
    public function payload(): Payload;
}
