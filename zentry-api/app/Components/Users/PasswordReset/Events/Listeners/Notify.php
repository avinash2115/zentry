<?php

namespace App\Components\Users\PasswordReset\Events\Listeners;

use App\Assistants\Notifications\Mail;
use App\Components\Users\PasswordReset\Events\Created;
use App\Components\Users\PasswordReset\Mails\Created as CreatedMail;

/**
 * Class Notify
 *
 * @package App\Components\Users\PasswordReset\Events\Listeners
 */
class Notify
{
    /**
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param Created $event
     *
     * @return void
     */
    public function handle(Created $event)
    {
        Mail::send($event->passwordReset()->user()->email(), new CreatedMail($event->passwordReset()));
    }
}