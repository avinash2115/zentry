<?php

namespace App\Components\Users\User\Storage;

use App\Components\Users\User\UserReadonlyContract;
use App\Convention\ValueObjects\Config\Config;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Exception;
use InvalidArgumentException;

/**
 * Class StorageEntity
 *
 * @package App\Components\Users\User\Storage
 */
class StorageEntity implements StorageContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

    const MEGABYTES_LIMIT = 400;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var string
     */
    private string $driver;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var bool
     */
    private bool $enabled;

    /**
     * @var int
     */
    private int $used;

    /**
     * @var int
     */
    private int $capacity;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     * @param Config               $config
     * @param string               $driver
     * @param string               $name
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        UserReadonlyContract $user,
        Config $config,
        string $driver,
        string $name
    ) {
        $this->setIdentity($identity);
        $this->user = $user;

        $this->setConfig($config)->setDriver($driver)->setName($name);

        $this->disable();
        $this->changeUsed(0);
        $this->changeCapacity(0);

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function driver(): string
    {
        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function isDriver(string $driver): bool
    {
        return $this->driver() === $driver;
    }

    /**
     * @param string $driver
     *
     * @return StorageEntity
     * @throws InvalidArgumentException
     */
    private function setDriver(string $driver): StorageEntity
    {
        if (!Arr::has(self::AVAILABLE_DRIVERS, $driver)) {
            throw new InvalidArgumentException("Driver {$driver} is now allowed");
        }

        $this->driver = $driver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function config(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     *
     * @return StorageEntity
     */
    private function setConfig(Config $config): StorageEntity
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function changeName(string $name): StorageContract
    {
        return $this->setName($name);
    }

    /**
     * @param string $name
     *
     * @return StorageEntity
     * @throws InvalidArgumentException
     */
    private function setName(string $name): StorageEntity
    {
        if (strEmpty($name)) {
            throw new InvalidArgumentException("Name can't be empty");
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return StorageEntity
     */
    private function setEnabled(bool $enabled): StorageEntity
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enable(): StorageContract
    {
        return $this->setEnabled(true);
    }

    /**
     * @inheritDoc
     */
    public function disable(): StorageContract
    {
        return $this->setEnabled(false);
    }

    /**
     * @inheritDoc
     */
    public function used(): int
    {
        return $this->used;
    }

    /**
     * @inheritDoc
     */
    public function changeUsed(int $used): StorageContract
    {
        $this->used = $used;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function capacity(): int
    {
        return $this->capacity;
    }

    /**
     * @inheritDoc
     */
    public function changeCapacity(int $capacity): StorageContract
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function available(): bool
    {
        if ($this->capacity === 0) {
            return true;
        }

        return ($this->capacity() - $this->used()) / 1048576 > self::MEGABYTES_LIMIT;
    }
}
