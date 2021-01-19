<?php

namespace App\Components\Users\PasswordReset\Events;

use App\Components\Users\PasswordReset\PasswordResetReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Queue\SerializesModels;

/**
 * Class Created
 *
 * @package App\Components\Users\PasswordReset\Events
 */
class Created
{
    use SerializesModels;

    /**
     * @var PasswordResetReadonlyContract
     */
    private PasswordResetReadonlyContract $passwordReset;

    /**
     * @param PasswordResetReadonlyContract $passwordReset
     */
    public function __construct(PasswordResetReadonlyContract $passwordReset)
    {
        $this->passwordReset = $passwordReset;
    }

    /**
     * @return PasswordResetReadonlyContract
     * @throws PropertyNotInit
     */
    public function passwordReset(): PasswordResetReadonlyContract
    {
        if (!$this->passwordReset instanceof PasswordResetReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->passwordReset;
    }
}
