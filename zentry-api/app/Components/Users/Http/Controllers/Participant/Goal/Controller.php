<?php

namespace App\Components\Users\Http\Controllers\Participant\Goal;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Http\Controllers\Participant\Traits\BulkProcessingTrait;
use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Participant\Goal
 */
class Controller extends BaseController
{
    use ParticipantServiceTrait;
    use AuthServiceTrait;
    use UserServiceTrait;
    use BulkProcessingTrait;

    /**
     * @param string $participantId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function index(string $participantId): Response
    {
        return $this->sendResponse(
            $this->participantService__()->workWith($participantId)->goalService()->list()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $participantId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function create(JsonApi $jsonApi, string $participantId): Response
    {
        $data = $jsonApi->attributes()->toArray();

        if ($jsonApi->relation('iep') instanceof JsonApi) {
            Arr::set($data, 'iep', $jsonApi->relation('iep')->id());
        }

        $this->participantService__()->workWith($participantId)->goalService()->create($data);

        $this->createTrackers($this->participantService__()->goalService()->trackerService(), $jsonApi);

        return $this->sendResponse(
            $this->participantService__()->goalService()->dto(),
            201
        );
    }

    /**
     * @param string $participantId
     * @param string $goalId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function show(string $participantId, string $goalId): Response
    {
        return $this->sendResponse(
            $this->participantService__()->workWith($participantId)->goalService()->workWith($goalId)->dto()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $participantId
     * @param string  $goalId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function change(JsonApi $jsonApi, string $participantId, string $goalId): Response
    {
        $this->participantService__()->workWith($participantId)->goalService()->workWith($goalId);

        $existed = $this->participantService__()->goalService()->trackerService()->listRO()->keyBy(
            static function (TrackerReadonlyContract $tracker) {
                return $tracker->identity()->toString();
            }
        );

        $jsonApi->relations('trackers')->each(
            function (JsonApi $jsonApi) use ($existed) {
                try {
                    $this->participantService__()->goalService()->trackerService()->workWith($jsonApi->id())->change(
                        $jsonApi->attributes()->toArray()
                    );
                    $existed->forget($jsonApi->id());
                } catch (NotFoundException $exception) {
                    $this->participantService__()->goalService()->trackerService()->create(
                        $jsonApi->attributes()->toArray()
                    );
                }
            }
        );

        $existed->each(
            function (TrackerReadonlyContract $tracker) {
                $this->participantService__()->goalService()->trackerService()->workWith($tracker->identity())->remove(
                );
            }
        );

        $data = $jsonApi->attributes()->toArray();

        if ($jsonApi->relation('iep') instanceof JsonApi) {
            Arr::set($data, 'iep', $jsonApi->relation('iep')->id());
        }

        return $this->sendResponse(
            $this->participantService__()->goalService()->change($data)->dto()
        );
    }

    /**
     * @param string $participantId
     * @param string $goalId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function remove(string $participantId, string $goalId): Response
    {
        $this->participantService__()->workWith($participantId)->goalService()->workWith($goalId)->remove();

        return $this->acknowledgeResponse();
    }
}
