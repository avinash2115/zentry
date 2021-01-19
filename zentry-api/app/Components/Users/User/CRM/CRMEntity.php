<?php

namespace App\Components\Users\User\CRM;

use App\Components\CRM\Source\SourceContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\CRM\Config\Config;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class CRMEntity
 *
 * @package App\Components\Users\User\CRM
 */
class CRMEntity implements CRMContract
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
     * @var bool
     */
    private bool $active;

    /**
     * @var bool
     */
    private bool $notified;

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
     * @param Config               $config
     * @param string               $driver
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        UserReadonlyContract $user,
        Config $config,
        string $driver
    ) {
        $this->setIdentity($identity);
        $this->user = $user;

        $this->setConfig($config);
        $this->setDriver($driver);

        $this->disable();
        $this->clearNotified();

        $this->setCreatedAt();
        $this->setUpdatedAt();
        $this->sources = new ArrayCollection();
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
        return self::AVAILABLE_DRIVERS[$this->driver];
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
     * @return CRMEntity
     * @throws InvalidArgumentException
     */
    private function setDriver(string $driver): CRMEntity
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
     * @inheritDoc
     */
    public function changeConfig(Config $value): CRMContract
    {
        return $this->setConfig($value);
    }

    /**
     * @param Config $config
     *
     * @return CRMEntity
     */
    private function setConfig(Config $config): CRMEntity
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function active(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $enabled
     *
     * @return CRMEntity
     */
    private function setActive(bool $enabled): CRMEntity
    {
        $this->active = $enabled;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enable(): CRMContract
    {
        return $this->setActive(true);
    }

    /**
     * @inheritDoc
     */
    public function disable(): CRMContract
    {
        return $this->setActive(false);
    }

    /**
     * @inheritDoc
     */
    public function notified(): bool
    {
        return $this->notified;
    }

    /**
     * @param bool $value
     *
     * @return CRMEntity
     */
    private function setNotified(bool $value): CRMEntity
    {
        $this->notified = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function markNotified(): CRMContract
    {
        return $this->setNotified(true);
    }

    /**
     * @inheritDoc
     */
    public function clearNotified(): CRMContract
    {
        return $this->setNotified(false);
    }

    /**
     * @inheritDoc
     */
    public function sources(): Collection
    {
        return $this->doctrineCollectionToCollection($this->sources);
    }

    /**
     * @inheritDoc
     */
    public function sourcesByIdentity(Identity $identity): SourceContract
    {
        $entity = $this->sources()->get($identity->toString());

        if (!$entity instanceof SourceContract) {
            throw new NotFoundException('Source not found');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function sourcesByCRM(Identity $identity): SourceContract
    {
        $entity = $this->sources()->first(
            static function (SourceContract $source) use ($identity) {
                return $source->crm()->identity()->equals($identity);
            }
        );

        if (!$entity instanceof SourceContract) {
            throw new NotFoundException('Source not found');
        }

        return $entity;
    }
}
