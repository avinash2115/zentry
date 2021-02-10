<?php

namespace App\Components\Users\User\Events\Listeners\CRM\Connection\Lost;

use App\Assistants\Notifications\Mail;
use App\Components\Users\User\Events\CRM\Connection\Lost as ConnectionLostEvent;
use App\Components\Users\User\Mails\CRM\ConnectionLost as ConnectionLostMail;

/**
 * Class Notify
 *
 * @package App\Components\Users\User\Events\Listeners\CRM\Connection\Lost
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
     * @param ConnectionLostEvent $event
     *
     * @return void
     */
    public function handle(ConnectionLostEvent $event)
    {
        Mail::send($event->user()->email(), new ConnectionLostMail($event->user(), $event->crm()));
    }
}
