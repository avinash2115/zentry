<?php

namespace App\Components\Users\Team\Request\Events\Listeners\Applied;

use App\Assistants\Notifications\Mail;
use App\Components\Users\Team\Request\Events\Applied;
use App\Components\Users\Team\Request\Mails\Applied as MailsApplied;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use InvalidArgumentException;

/**
 * Class Notify
 *
 * @package App\Components\Users\Team\Request\Events\Listeners\Applied
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
     * @param Applied $event
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     */
    public function handle(Applied $event): void
    {
        Mail::send($event->team()->owner()->email(), new MailsApplied($event->team(), $event->request()));
    }
}
