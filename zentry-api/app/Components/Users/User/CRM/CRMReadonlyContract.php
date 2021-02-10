<?php

namespace App\Components\Users\User\CRM;

use App\Components\Users\ValueObjects\CRM\Config\Config;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use Illuminate\Support\Collection;

/**
 * Interface CRMReadonlyContract
 *
 * @package App\Components\Users\User\CRM
 */
interface CRMReadonlyContract extends IdentifiableContract, TimestampableContract
{
    public const DRIVER_THERAPYLOG = 'therapylog';

    public const LABEL_THERAPYLOG = 'Therapylog';

    public const AVAILABLE_DRIVERS = [
        self::DRIVER_THERAPYLOG => self::LABEL_THERAPYLOG,
    ];

    /**
     * @return Config
     */
    public function config(): Config;

    /**
     * @return string
     */
    public function driver(): string;

    /**
     * @return string
     */
    public function driverLabel(): string;

    /**
     * @param string $driver
     *
     * @return bool
     */
    public function isDriver(string $driver): bool;

    /**
     * @return bool
     */
    public function active(): bool;

    /**
     * @return bool
     */
    public function notified(): bool;

    /**
     * @return Collection
     */
    public function sources(): Collection;
}
