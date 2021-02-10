<?php

namespace App\Components\Sessions\Session\Events\Listeners\Elastic;

use App\Components\Sessions\Jobs\Elastic\Indexing\Reindex as JobReindex;
use App\Components\Users\Participant\Events\Changed;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;

/**
 * Class Reindex
 *
 * @package App\Components\Sessions\Session\Events\Listeners
 */
class Reindex
{
    /**
     * @param Changed $event
     *
     * @return void
     * @throws PropertyNotInit
     */
    public function handle(Changed $event): void
    {
        dispatch(new JobReindex($event->participant()->identity()))->delay(5);
    }
}
