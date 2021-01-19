<?php

namespace App\Components\Users\User\Mails;

use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Convention\Helpers\Links;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class Created
 *
 * @package App\Users\User\Mails
 */
class Created extends Mailable
{
    use Queueable;
    use SerializesModels;

    public const LOGIN_LINK = '/auth/login';

    /**
     * @var string
     */
    public $subject = 'Your account has been registered!';

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
     * @return $this
     * @throws Throwable
     */
    public function build()
    {
        return $this->view('emails.user.signup.confirmation.welcome')->with(
            [
                'loginLink' => (new Links())->url(
                    self::LOGIN_LINK,
                ),
                'user' => $this->user,
                'password' => $this->credentials->password()->raw(),
            ]
        );
    }
}
