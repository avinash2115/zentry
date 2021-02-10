<?php

namespace App\Components\Users\PasswordReset\Mails;

use App\Components\Users\PasswordReset\PasswordResetReadonlyContract;
use App\Convention\Helpers\Links;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Class Created
 *
 * @package App\Users\PasswordReset\Mails
 */
class Created extends Mailable
{
    use Queueable;
    use SerializesModels;

    const LINK = '/auth/forgot/{id}';

    /**
     * @var string
     */
    public $subject = 'Password Reset Request';

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
     * @return $this
     * @throws Throwable
     */
    public function build()
    {
        return $this->view('emails.user.password.reset')->with(
            [
                'passwordResetLink' => (new Links())->url(
                    self::LINK,
                    ['{id}'],
                    [$this->passwordReset->identity()->toString()]
                ),
                'user' => $this->passwordReset->user(),
            ]
        );
    }
}
