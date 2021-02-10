<?php

namespace App\Components\Users\User\Events;

use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Queue\SerializesModels;

/**
 * Class Created
 */
class Created
{
    use SerializesModels;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @var Credentials
     */
    private Credentials $credentials;

    /**
     * @param UserReadonlyContract $user
     * @param Credentials          $credentials
     */
    public function __construct(UserReadonlyContract $user, Credentials $credentials)
    {
        $this->user = $user;
        $this->credentials = $credentials;
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
     * @return Credentials
     * @throws PropertyNotInit
     */
    public function credentials(): Credentials
    {
        if (!$this->credentials instanceof Credentials) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->credentials;
    }
}
