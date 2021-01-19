<?php

namespace App\Assistants\Elastic\Events\Listeners\System;

use App\Assistants\Elastic\Events\StateDeletion;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Jobs\RemoveEntityFromIndex;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Queueable;

/**
 * Class RemoveDocument
 *
 * @package App\Assistants\Elastic\Events\Listeners\System
 */
class RemoveDocument
{
    use ElasticServiceTrait;

    /**
     * @param StateDeletion $event
     */
    public function handle(StateDeletion $event): void
    {
        if (!app()->runningUnitTests()) {

            collect(Index::AVAILABLE_INDEXES)->each(
                static function (string $index) use ($event) {
                    try {
                        $indexVO = new Index($index);

                        if ($event->withJob()) {
                            dispatch(
                                app()->make(
                                    RemoveEntityFromIndex::class,
                                    [
                                        'index' => $indexVO,
                                        'queueable' => new Queueable(
                                            $event->indexable()->asIdentity(),
                                            $event->indexable()->asType()
                                        ),
                                    ]
                                )
                            );
                        } else {
                            app()->make(
                                RemoveEntityFromIndex::class,
                                [
                                    'index' => $indexVO,
                                    'queueable' => new Queueable(
                                        $event->indexable()->asIdentity(),
                                        $event->indexable()->asType()
                                    ),
                                ]
                            )->handleSilent();
                        }
                    } catch (IndexNotSupported $exception) {
                        report($exception);
                    }
                }
            );

        }
    }
}
