<?php

namespace App\Assistants\Files\Drivers\Kloudless;

use App\Assistants\Files\Drivers\Kloudless\Connection\ApiClient;
use App\Assistants\Files\Drivers\Kloudless\Connection\Client;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Convention\ValueObjects\Config\Config;
use App\Convention\ValueObjects\Config\Option;
use Arr;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use League\Flysystem\Filesystem;
use RuntimeException;

/**
 * Class Extender
 *
 * @package App\Assistants\Files\Drivers\Kloudless
 */
class Extender
{
    /**
     * @param string $driver
     * @param Config $config
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public static function extend(string $driver, Config $config): void
    {
        if (!in_array($driver, StorageReadonlyContract::KLOUDLESS_GROUP, true)) {
            throw new InvalidArgumentException("Driver {$driver} is not allowed");
        }

        $accountId = $config->options()->first(
            static function (Option $option) {
                return $option->isType('id');
            }
        );

        if (!$accountId instanceof Option) {
            throw new RuntimeException('Account id option not found at the user storage config');
        }

        self::mergeDiskOptions(['account_id' => $accountId], StorageReadonlyContract::DRIVER_KLOUDLESS_PREFIX);

        config(['filesystems.cloud' => StorageReadonlyContract::DRIVER_KLOUDLESS_PREFIX]);

        Storage::forgetDisk(StorageReadonlyContract::DRIVER_KLOUDLESS_PREFIX);

        Storage::extend(
            StorageReadonlyContract::DRIVER_KLOUDLESS_PREFIX,
            static function ($app, $config) use ($accountId) {
                return new Filesystem(new Adapter(new Client(new ApiClient($accountId->value()))));
            }
        );
    }

    /**
     * @param array  $options
     * @param string $type
     */
    public static function mergeDiskOptions(array $options, string $type): void
    {
        $filesystemDisks = config('filesystems.disks', []);

        Arr::set($options, 'driver', $type);
        Arr::set($filesystemDisks, $type, $options);

        config(['filesystems.disks' => $filesystemDisks]);
    }
}
