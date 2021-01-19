<?php

namespace App\Components\Users\User\Events\Listeners;

use App\Assistants\Notifications\Mail;
use App\Components\Users\User\Events\Created;
use App\Components\Users\User\Mails\Created as CreatedMail;

/**
 * Class Notify
 *
 * @package App\Components\Users\User\Events
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
        Mail::send($event->user()->email(), new CreatedMail($event->user(), $event->credentials()));
    }
}