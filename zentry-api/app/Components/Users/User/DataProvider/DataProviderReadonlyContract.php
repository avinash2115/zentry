<?php

namespace App\Components\Users\User\DataProvider;

use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use App\Convention\ValueObjects\Config\Config;

/**
 * Interface DataProviderReadonlyContract
 *
 * @package App\Components\Users\User\DataProvider
 */
interface DataProviderReadonlyContract extends IdentifiableContract, TimestampableContract
{
    public const STATUS_DISABLED = 0;
    public const STATUS_ENABLED = 10;
    public const STATUS_NOT_AUTHORIZED = 20;

    public const STATUSES_AVAILABLE = [
        self::STATUS_DISABLED,
        self::STATUS_ENABLED,
        self::STATUS_NOT_AUTHORIZED,
    ];

    public const DRIVER_GOOGLE_CALENDAR = 'google_calendar';

    public const LABEL_GOOGLE_CALENDAR = 'Google Calendar';

    public const DRIVERS_AVAILABLE = [
        self::DRIVER_GOOGLE_CALENDAR => self::LABEL_GOOGLE_CALENDAR,
    ];

    public const CONFIG_AUTH_CODE_KEY = 'authorizationCode';
    public const CONFIG_ACCESS_TOKEN_KEY = 'access_token';
    public const CONFIG_EMAIL_KEY = 'email';

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
    public function isEnabled(): bool;

    /**
     * @inheritDoc
     */
    public function isDisabled(): bool;

    /**
     * @inheritDoc
     */
    public function isNotAuthorized(): bool;

    /**
     * @inheritDoc
     */
    public function isStatus(int $status): bool;

    /**
     * @return int
     */
    public function status(): int;
}
