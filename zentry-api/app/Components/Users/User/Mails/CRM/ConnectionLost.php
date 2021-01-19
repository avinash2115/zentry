<?php

namespace App\Components\Users\User\Mails\CRM;

use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class ConnectionLost
 *
 * @package App\Users\User\Mails
 */
class ConnectionLost extends Mailable
{
    use Queueable;
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
     * @param CRMReadonlyContract  $crm
     */
    public function __construct(UserReadonlyContract $user, CRMReadonlyContract $crm)
    {
        $this->user = $user;
        $this->crm = $crm;
    }

    /**
     * @return $this
     * @throws Throwable
     */
    public function build()
    {
        return $this
            ->subject("Your {$this->crm->driverLabel()} has authorization issues!")
            ->view('emails.user.crm.connection_lost')->with(
            [
                'user' => $this->user,
                'crm' => $this->crm,
            ]
        );
    }
}
