<?php

namespace App\Assistants\Notifications;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail as IlluminateMail;

/**
 * Class Mail
 *
 * @package App\Assistants\Notifications
 */
class Mail
{
    /**
     * @param string   $to
     * @param Mailable $mail
     */
    public static function send(string $to, Mailable $mail): void
    {
        IlluminateMail::to($to)->send($mail);
    }
}