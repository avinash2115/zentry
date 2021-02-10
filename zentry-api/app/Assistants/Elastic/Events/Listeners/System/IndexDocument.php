<?php

namespace App\Assistants\Elastic\Events\Listeners\System;

use App\Assistants\Elastic\Events\StateCreateOrUpdate;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Jobs\AddOrUpdateEntityToIndex;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Index;

/**
 * Class IndexDocument
 *
 * @package App\Assistants\Elastic\Events\Listeners\System
 */
class IndexDocument
{
    use ElasticServiceTrait;

    /**
     * @param StateCreateOrUpdate $event
     *
     */
    public function handle(StateCreateOrUpdate $event): void
    {
        if (!app()->runningUnitTests() && app()->environment() !== 'build') {
            collect(Index::AVAILABLE_INDEXES)->each(
                static function (string $index) use ($event) {
                    try {
                        $indexVO = new Index($index);

                        if ($event->withJob()) {
                            dispatch(
                                app()->make(
                                    AddOrUpdateEntityToIndex::class,
                                    [
                                        'index' => $indexVO,
                                        'queueable' => $event->indexable()->forQueue($indexVO),
                                    ]
                                )
                            );
                        } else {
                            app()->make(
                                AddOrUpdateEntityToIndex::class,
                                [
                                    'index' => $indexVO,
                                    'queueable' => $event->indexable()->forQueue($indexVO),
                                ]
                            )->handleSilent();
                        }
                    } catch (IndexNotSupported $exception) {
                    }
                }
            );
        }
    }
}
