<?php

namespace App\Components\Users\User;

use App\Components\Users\User\DataProvider\DataProviderContract;
use App\Components\Users\User\CRM\CRMContract;
use App\Components\Users\User\Storage\StorageContract;
use App\Components\Users\User\Poi\PoiContract;
use App\Components\Users\User\Backtrack\BacktrackContract;
use App\Components\Users\User\Profile\ProfileContract;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Convention\Entities\Contracts\ArchivableContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface UserContract
 *
 * @package App\Components\Users\User
 */
interface UserContract extends UserReadonlyContract, ArchivableContract
{
    /**
     * @return ProfileContract
     * @throws InvalidArgumentException
     */
    public function profile(): ProfileContract;

    /**
     * @param ProfileContract $profile
     *
     * @return UserContract
     * @throws InvalidArgumentException
     */
    public function attachProfile(ProfileContract $profile): UserContract;

    /**
     * @param HashedPassword $password
     *
     * @return UserContract
     */
    public function changePassword(HashedPassword $password): UserContract;

    /**
     * @param Email $email
     *
     * @return UserContract
     */
    public function changeEmail(Email $email): UserContract;

    /**
     * @param PoiContract $poi
     *
     * @return UserContract
     */
    public function attachPoi(PoiContract $poi): UserContract;

    /**
     * @param BacktrackContract $backtrack
     *
     * @return UserContract
     * @throws RuntimeException
     */
    public function attachBackTrack(BacktrackContract $backtrack): UserContract;

    /**
     * @param StorageContract $storage
     *
     * @return UserContract
     * @throws RuntimeException
     */
    public function addStorage(StorageContract $storage): UserContract;

    /**
     * @param Identity $storage
     *
     * @return StorageContract
     * @throws NotFoundException
     */
    public function storageByIdentity(Identity $storage): StorageContract;

    /**
     * @param string $driver
     *
     * @return StorageContract
     * @throws NotFoundException
     */
    public function storageByDriver(string $driver): StorageContract;

    /**
     * @param StorageContract $storage
     *
     * @return UserContract
     * @throws NotFoundException|RuntimeException
     */
    public function removeStorage(StorageContract $storage): UserContract;

    /**
     * @param DataProviderContract $dataProvider
     *
     * @return UserContract
     * @throws RuntimeException
     */
    public function addDataProvider(DataProviderContract $dataProvider): UserContract;

    /**
     * @param Identity $dataProvider
     *
     * @return DataProviderContract
     * @throws NotFoundException
     */
    public function dataProviderByIdentity(Identity $dataProvider): DataProviderContract;

    /**
     * @param string $driver
     *
     * @return DataProviderContract
     * @throws NotFoundException
     */
    public function dataProviderByDriver(string $driver): DataProviderContract;

    /**
     * @param DataProviderContract $dataProvider
     *
     * @return UserContract
     * @throws NotFoundException|RuntimeException
     */
    public function removeDataProvider(DataProviderContract $dataProvider): UserContract;

    /**
     * @param CRMContract $crm
     *
     * @return UserContract
     * @throws RuntimeException
     */
    public function connectCRM(CRMContract $crm): UserContract;

    /**
     * @param Identity $crm
     *
     * @return CRMContract
     * @throws NotFoundException
     */
    public function crmByIdentity(Identity $crm): CRMContract;

    /**
     * @param string $driver
     *
     * @return CRMContract
     * @throws NotFoundException
     */
    public function crmByDriver(string $driver): CRMContract;

    /**
     * @param CRMContract $crm
     *
     * @return UserContract
     * @throws NotFoundException|RuntimeException
     */
    public function disconnectCRM(CRMContract $crm): UserContract;
}
