<?php

namespace App\Components\CRM\Source;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface SourceReadonlyContract
 *
 * @package App\Components\CRM\Source
 */
interface SourceReadonlyContract extends IdentifiableContract, TimestampableContract
{
    public const DIRECTION_IN = 'IN';

    public const DIRECTION_OUT = 'OUT';

    public const AVAILABLE_DIRECTIONS = [
        self::DIRECTION_IN,
        self::DIRECTION_OUT,
    ];

    /**
     * @return string
     */
    public function direction(): string;

    /**
     * @return string
     */
    public function sourceId(): string;

    /**
     * @return CRMReadonlyContract
     */
    public function crm(): CRMReadonlyContract;

    /**
     * @return CRMImportableContract
     */
    public function owner(): CRMImportableContract;
}
