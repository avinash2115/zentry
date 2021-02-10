<?php

namespace App\Components\Users\User\Storage;

use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use App\Convention\ValueObjects\Config\Config;

/**
 * Interface StorageReadonlyContract
 *
 * @package App\Components\Users\User\Storage
 */
interface StorageReadonlyContract extends IdentifiableContract, TimestampableContract
{
    public const DRIVER_KLOUDLESS_PREFIX = 'kloudless';

    public const DRIVER_DEFAULT = 'default';

    public const DRIVER_KLOUDLESS_DROPBOX = self::DRIVER_KLOUDLESS_PREFIX . '_dropbox';

    public const DRIVER_KLOUDLESS_GOOGLE_DRIVE = self::DRIVER_KLOUDLESS_PREFIX . '_google_drive';

    public const DRIVER_KLOUDLESS_BOX = self::DRIVER_KLOUDLESS_PREFIX . '_box';

    public const LABEL_PLACEHOLDER_APP_NAME = '{APP_NAME}';

    public const LABEL_DEFAULT = self::LABEL_PLACEHOLDER_APP_NAME . ' Storage';

    public const LABEL_KLOUDLESS_GOOGLE_DRIVE = 'Google Drive';

    public const LABEL_KLOUDLESS_DROPBOX = 'Dropbox';

    public const LABEL_KLOUDLESS_BOX = 'Box';

    public const AVAILABLE_DRIVERS = [
        self::DRIVER_DEFAULT => self::LABEL_DEFAULT,
        self::DRIVER_KLOUDLESS_GOOGLE_DRIVE => self::LABEL_KLOUDLESS_GOOGLE_DRIVE,
        self::DRIVER_KLOUDLESS_DROPBOX => self::LABEL_KLOUDLESS_DROPBOX,
        self::DRIVER_KLOUDLESS_BOX => self::LABEL_KLOUDLESS_BOX,
    ];

    public const KLOUDLESS_GROUP = [
        self::DRIVER_KLOUDLESS_DROPBOX,
        self::DRIVER_KLOUDLESS_GOOGLE_DRIVE,
        self::DRIVER_KLOUDLESS_BOX,
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
     * @param string $driver
     *
     * @return bool
     */
    public function isDriver(string $driver): bool;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return bool
     */
    public function enabled(): bool;

    /**
     * @return bool
     */
    public function available(): bool;

    /**
     * @return int
     */
    public function used(): int;

    /**
     * @return int
     */
    public function capacity(): int;
}
