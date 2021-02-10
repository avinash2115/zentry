<?php

namespace App\Components\Users\User\DataProvider;

use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Config\Config;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Exception;
use InvalidArgumentException;

/**
 * Class DataProviderEntity
 *
 * @package App\Components\Users\User\DataProvider
 */
class DataProviderEntity implements DataProviderContract
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use CollectibleTrait;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var string
     */
    private string $driver;

    /**
     * @var int
     */
    private int $status;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $sources;

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     * @param string               $driver
     * @param Config               $config
     * @param int                  $status
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        UserReadonlyContract $user,
        string $driver,
        Config $config,
        int $status = 0
    ) {
        $this->setIdentity($identity);
        $this->user = $user;
        $this->setConfig($config);
        $this->setDriver($driver);
        $this->setStatus($status);

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
    public function driverLabel(): string
    {
        return self::DRIVERS_AVAILABLE[$this->driver];
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
     * @return DataProviderEntity
     * @throws InvalidArgumentException
     */
    private function setDriver(string $driver): DataProviderEntity
    {
        if (!Arr::has(self::DRIVERS_AVAILABLE, $driver)) {
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
     * @inheritDoc
     */
    public function changeConfig(Config $value): DataProviderContract
    {
        return $this->setConfig($value);
    }

    /**
     * @param Config $config
     *
     * @return DataProviderEntity
     */
    private function setConfig(Config $config): DataProviderEntity
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return DataProviderEntity
     * @throws InvalidArgumentException
     */
    private function setStatus(int $status): DataProviderEntity
    {
        if (!in_array($status, self::STATUSES_AVAILABLE, true)) {
            throw new InvalidArgumentException("Status {$status} is not available");
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enable(): DataProviderContract
    {
        return $this->setStatus(self::STATUS_ENABLED);
    }

    /**
     * @inheritDoc
     */
    public function disable(): DataProviderContract
    {
        return $this->setStatus(self::STATUS_DISABLED);
    }

    /**
     * @inheritDoc
     */
    public function notAuthorized(): DataProviderContract
    {
        return $this->setStatus(self::STATUS_NOT_AUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return $this->isStatus(self::STATUS_ENABLED);
    }

    /**
     * @inheritDoc
     */
    public function isDisabled(): bool
    {
        return $this->isStatus(self::STATUS_DISABLED);
    }

    /**
     * @inheritDoc
     */
    public function isNotAuthorized(): bool
    {
        return $this->isStatus(self::STATUS_NOT_AUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function isStatus(int $status): bool
    {
        return $this->status() === $status;
    }
}
