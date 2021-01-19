<?php

namespace App\Components\Users\Team\Request\Events\Listeners\Rejected;

use App\Assistants\Notifications\Mail;
use App\Components\Users\Team\Request\Events\Rejected;
use App\Components\Users\Team\Request\Mails\Rejected as MailsRejected;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use InvalidArgumentException;

/**
 * Class Notify
 *
 * @package App\Components\Users\Team\Request\Events\Listeners\Rejected
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
     * @param Rejected $event
     *
     * @return void
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function handle(Rejected $event): void
    {
        Mail::send($event->team()->owner()->email(), new MailsRejected($event->team(), $event->request()));
    }
}
