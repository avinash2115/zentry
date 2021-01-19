<?php

namespace App\Components\Users\Http\Controllers\Participant\Traits;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Participant\Goal\GoalServiceContract;
use App\Components\Users\Services\Participant\Goal\Tracker\TrackerServiceContract;

/**
 * Trait BulkProcessingTrait
 *
 * @package App\Components\Users\Http\Controllers\Participant\Traits
 */
trait BulkProcessingTrait
{
    /**
     * @param GoalServiceContract $goalService
     * @param JsonApi             $jsonApi
     */
    private function createGoals(GoalServiceContract $goalService, JsonApi $jsonApi): void
    {
        $jsonApi->relations('goals')->each(
            function (JsonApi $jsonApi) use ($goalService) {
                $goalService->create($jsonApi->attributes()->toArray());
                $this->createTrackers($goalService->trackerService(), $jsonApi);
            }
        );
    }

    /**
     * @param TrackerServiceContract $trackerService
     * @param JsonApi                $jsonApi
     */
    private function createTrackers(TrackerServiceContract $trackerService, JsonApi $jsonApi): void
    {
        if ($jsonApi->relations('trackers')->isNotEmpty()) {
            $jsonApi->relations('trackers')->each(
                static function (JsonApi $jsonApi) use ($trackerService) {
                    $trackerService->create(
                        [
                            'name' => $jsonApi->attributes()->get('name'),
                            'type' => $jsonApi->attributes()->get('type'),
                            'icon' => $jsonApi->attributes()->get('icon'),
                            'color' => $jsonApi->attributes()->get('color'),
                        ]
                    );
                }
            );
        } else {
            $trackerService->createDefault();
        }
    }
}
