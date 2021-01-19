<?php

namespace App\Components\Users\User\Poi;

/**
 * Interface PoiContract
 *
 * @package App\Components\Users\User\Poi
 */
interface PoiContract extends PoiReadonlyContract
{
    public const DEFAULT_BACKWARD = 5;
    public const DEFAULT_FORWARD = 5;
}
