<?php

namespace App\Components\Users\User\Events\CRM\Connection;

use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Queue\SerializesModels;

/**
 * Class Lost
 */
class Lost
{
    use SerializesModels;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @var CRMReadonlyContract
     */
    private CRMReadonlyContract $crm;

    /**
     * @param UserReadonlyContract $user
     * @param CRMReadonlyContract $crm
     */
    public function __construct(UserReadonlyContract $user, CRMReadonlyContract $crm)
    {
        $this->user = $user;
        $this->crm = $crm;
    }

    /**
     * @return UserReadonlyContract
     * @throws PropertyNotInit
     */
    public function user(): UserReadonlyContract
    {
        if (!$this->user instanceof UserReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->user;
    }

    /**
     * @return CRMReadonlyContract
     * @throws PropertyNotInit
     */
    public function crm(): CRMReadonlyContract
    {
        if (!$this->crm instanceof CRMReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->crm;
    }

}
