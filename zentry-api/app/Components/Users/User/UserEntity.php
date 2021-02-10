<?php

namespace App\Components\Users\User;

use App\Components\Users\User\DataProvider\DataProviderContract;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use App\Components\Users\User\CRM\CRMContract;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\Storage\StorageContract;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\Backtrack\BacktrackContract;
use App\Components\Users\User\Backtrack\BacktrackReadonlyContract;
use App\Components\Users\User\Poi\PoiContract;
use App\Components\Users\User\Poi\PoiReadonlyContract;
use App\Components\Users\User\Profile\ProfileContract;
use App\Components\Users\User\Profile\ProfileReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Entities\Traits\{ArchivableTrait, CollectibleTrait, IdentifiableTrait, TimestampableTrait};
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use Doctrine\Common\Collections\Collection as DoctrineCollection;

/**
 * Class UserEntity
 *
 * @package App\Components\Users\User
 */
class UserEntity implements UserContract
{
    use ArchivableTrait;
    use IdentifiableTrait;
    use TimestampableTrait;
    use CollectibleTrait;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var ProfileContract|null
     */
    private ?ProfileContract $profile = null;

    /**
     * @var PoiContract|null
     */
    private ?PoiContract $poi = null;

    /**
     * @var BacktrackContract|null
     */
    private ?BacktrackContract $backtrack = null;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $storages;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $crms;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $sso;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $dataProviders;

    /**
     * @param Identity    $identity
     * @param Credentials $credentials
     *
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        Credentials $credentials
    ) {
        $this->setIdentity($identity);

        $this->setEmail($credentials->email())->setPassword($credentials->password());

        $this->storages = new ArrayCollection();
        $this->crms = new ArrayCollection();
        $this->sso = new ArrayCollection();
        $this->dataProviders = new ArrayCollection();

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @param Email $email
     *
     * @return UserEntity
     */
    private function setEmail(Email $email): UserEntity
    {
        $this->email = $email->toString();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function changeEmail(Email $email): UserContract
    {
        return $this->setEmail($email);
    }

    /**
     * @inheritDoc
     */
    public function password(): string
    {
        return $this->password;
    }

    /**
     * @param HashedPassword $password
     *
     * @return UserEntity
     */
    private function setPassword(HashedPassword $password): UserEntity
    {
        $this->password = $password->toString();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changePassword(HashedPassword $password): UserContract
    {
        $this->setPassword($password);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function attachPoi(PoiContract $poi): UserContract
    {
        if (!$this->poi instanceof PoiContract) {
            $this->poi = $poi;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function poi(): PoiReadonlyContract
    {
        if (!$this->poi instanceof PoiReadonlyContract) {
            $this->poi = app()->make(
                PoiContract::class,
                [
                    'identity' => IdentityGenerator::next(),
                    'user' => $this,
                    'backward' => PoiContract::DEFAULT_BACKWARD,
                    'forward' => PoiContract::DEFAULT_FORWARD,
                ]
            );
        }

        return $this->poi;
    }

    /**
     * @inheritDoc
     */
    public function attachBackTrack(BacktrackContract $backtrack): UserContract
    {
        if (!$this->backtrack instanceof BacktrackReadonlyContract) {
            $this->backtrack = $backtrack;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function backtrack(): BacktrackReadonlyContract
    {
        if (!$this->backtrack instanceof BacktrackReadonlyContract) {
            $this->backtrack = app()->make(
                BacktrackContract::class,
                [
                    'identity' => IdentityGenerator::next(),
                    'user' => $this,
                    'backward' => PoiContract::DEFAULT_BACKWARD,
                ]
            );
        }

        return $this->backtrack;
    }

    /**
     * @inheritDoc
     */
    public function fileNamespace(bool $humanReadable = false): string
    {
        return $this->identity()->toString();
    }

    /**
     * @inheritDoc
     */
    public function attachProfile(ProfileContract $profile): UserContract
    {
        if ($this->profile instanceof ProfileContract) {
            throw new InvalidArgumentException('Profile already assigned.');
        }

        return $this->setProfile($profile);
    }

    /**
     * @param ProfileContract $profile
     *
     * @return UserEntity
     */
    private function setProfile(ProfileContract $profile): UserEntity
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function profile(): ProfileContract
    {
        if (!$this->profile instanceof ProfileContract) {
            throw new InvalidArgumentException('Profile is not assigned.');
        }

        return $this->profile;
    }

    /**
     * @inheritDoc
     */
    public function profileReadonly(): ProfileReadonlyContract
    {
        return $this->profile();
    }

    /**
     * @inheritDoc
     */
    public function addStorage(StorageContract $storage): UserContract
    {
        $existed = $this->storages()->first(
            static function (StorageReadonlyContract $existed) use ($storage) {
                return $storage->driver() === $existed->driver();
            }
        );

        if ($existed instanceof StorageReadonlyContract) {
            throw new RuntimeException('Storage already exists');
        }

        $this->storages->set($storage->identity()->toString(), $storage);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function storageByIdentity(Identity $identity): StorageContract
    {
        $storage = $this->storages()->first(
            static function (StorageReadonlyContract $storage) use ($identity) {
                return $storage->identity()->equals($identity);
            }
        );

        if (!$storage instanceof StorageContract) {
            throw new NotFoundException();
        }

        return $storage;
    }

    /**
     * @inheritDoc
     */
    public function storageByDriver(string $driver): StorageContract
    {
        $storage = $this->storages()->first(
            static function (StorageReadonlyContract $storage) use ($driver) {
                return $storage->isDriver($driver);
            }
        );

        if (!$storage instanceof StorageContract) {
            throw new NotFoundException();
        }

        return $storage;
    }

    /**
     * @inheritDoc
     */
    public function removeStorage(StorageContract $storage): UserContract
    {
        $existed = $this->storageByIdentity($storage->identity());

        if ($existed->enabled()) {
            throw new RuntimeException('Trying to remove enabled storage');
        }

        if ($existed->isDriver(StorageReadonlyContract::DRIVER_DEFAULT)) {
            throw new RuntimeException('Trying to remove default storage');
        }

        $this->storages->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function storages(): Collection
    {
        return $this->doctrineCollectionToCollection($this->storages);
    }

    /**
     * @inheritDoc
     */
    public function enabledStorage(): StorageReadonlyContract
    {
        $enabled = $this->storages()->first(
            static function (StorageReadonlyContract $storage) {
                return $storage->enabled();
            }
        );

        if (!$enabled instanceof StorageReadonlyContract) {
            throw new NotFoundException();
        }

        return $enabled;
    }

    /**
     * @inheritDoc
     */
    public function addDataProvider(DataProviderContract $dataProvider): UserContract
    {
        $existed = null;

        try {
            $existed = $this->dataProviderByDriver($dataProvider->driver());
        } catch (NotFoundException $exception) {
            $this->dataProviders->set($dataProvider->identity()->toString(), $dataProvider);
        }

        if ($existed instanceof DataProviderReadonlyContract) {
            throw new RuntimeException('Data Provider already connected');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dataProviderByIdentity(Identity $identity): DataProviderContract
    {
        $dataProvider = $this->dataProviders()->first(
            static function (DataProviderReadonlyContract $dataProvider) use ($identity) {
                return $dataProvider->identity()->equals($identity);
            }
        );

        if (!$dataProvider instanceof DataProviderContract) {
            throw new NotFoundException();
        }

        return $dataProvider;
    }

    /**
     * @inheritDoc
     */
    public function dataProviderByDriver(string $driver): DataProviderContract
    {
        $dataProvider = $this->dataProviders()->first(
            static function (DataProviderReadonlyContract $dataProvider) use ($driver) {
                return $dataProvider->isDriver($driver);
            }
        );

        if (!$dataProvider instanceof DataProviderContract) {
            throw new NotFoundException();
        }

        return $dataProvider;
    }

    /**
     * @inheritDoc
     */
    public function removeDataProvider(DataProviderContract $dataProvider): UserContract
    {
        $this->dataProviders->remove($this->dataProviderByIdentity($dataProvider->identity())->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dataProviders(): Collection
    {
        return collect($this->dataProviders);
    }

    /**
     * @inheritDoc
     */
    public function connectCRM(CRMContract $crm): UserContract
    {
        $existed = null;
        try {
            $existed = $this->crmByDriver($crm->driver());
        } catch (NotFoundException $exception) {
            $this->crms->set($crm->identity()->toString(), $crm);
        }

        if ($existed instanceof CRMReadonlyContract) {
            throw new RuntimeException('CRM already connected');
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function crmByIdentity(Identity $identity): CRMContract
    {
        $crm = $this->crms()->first(
            static function (CRMReadonlyContract $crm) use ($identity) {
                return $crm->identity()->equals($identity);
            }
        );

        if (!$crm instanceof CRMContract) {
            throw new NotFoundException();
        }

        return $crm;
    }

    /**
     * @inheritDoc
     */
    public function crmByDriver(string $driver): CRMContract
    {
        $crm = $this->crms()->first(
            static function (CRMReadonlyContract $crm) use ($driver) {
                return $crm->isDriver($driver);
            }
        );

        if (!$crm instanceof CRMContract) {
            throw new NotFoundException();
        }

        return $crm;
    }

    /**
     * @inheritDoc
     */
    public function disconnectCRM(CRMContract $crm): UserContract
    {
        $existed = $this->crmByIdentity($crm->identity());

        $this->crms->remove($existed->identity()->toString());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function crms(): Collection
    {
        return $this->doctrineCollectionToCollection($this->crms);
    }

}
