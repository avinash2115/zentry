<?php

namespace App\Components\Users\Team\Request\Events\Listeners\Created;

use App\Assistants\Notifications\Mail;
use App\Components\Users\Team\Request\Events\Created;
use App\Components\Users\Team\Request\Mails\Created as MailsCreated;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use RuntimeException;

/**
 * Class Notify
 *
 * @package App\Components\Users\Team\Request\Events\Listeners\Created
 */
class Notify
{
    /**
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @param Created $event
     *
     * @return void
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function handle(Created $event): void
    {
        Mail::send($event->request()->user()->email(), new MailsCreated($event->team(), $event->request(), $event->link()));
    }
}
