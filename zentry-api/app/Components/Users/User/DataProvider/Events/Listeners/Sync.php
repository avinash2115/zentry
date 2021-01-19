<?php

namespace App\Components\Users\User\DataProvider\Events\Listeners;

use App\Components\Users\Jobs\DataProvider\Synchronize;
use App\Components\Users\User\DataProvider\Events\Created;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;

/**
 * Class Sync
 *
 * @package App\Components\Users\User\DataProvider\Events\Listeners
 */
class Sync
{
    /**
     * @param Created $event
     *
     * @return void
     * @throws PropertyNotInit
     */
    public function handle(Created $event): void
    {
        dispatch(new Synchronize($event->userIdentity(), $event->dataProvider()->identity()));
    }
}
