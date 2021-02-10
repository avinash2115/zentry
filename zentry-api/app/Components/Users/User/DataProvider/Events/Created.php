<?php

namespace App\Components\Users\User\DataProvider\Events;

use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class Created
 *
 * @package App\Components\Users\User\DataProvider\Events
 */
class Created
{
    /**
     * @var DataProviderReadonlyContract
     */
    private DataProviderReadonlyContract $dataProvider;

    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @param Identity                     $userIdentity
     * @param DataProviderReadonlyContract $dataProvider
     */
    public function __construct(Identity $userIdentity, DataProviderReadonlyContract $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->userIdentity = $userIdentity;
    }

    /**
     * @return DataProviderReadonlyContract
     * @throws PropertyNotInit
     */
    public function dataProvider(): DataProviderReadonlyContract
    {
        if (!$this->dataProvider instanceof DataProviderReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->dataProvider;
    }

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function userIdentity(): Identity
    {
        if (!$this->userIdentity instanceof Identity) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->userIdentity;
    }
}
