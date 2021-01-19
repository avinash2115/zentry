<?php

namespace App\Components\Users\Http\Controllers\Participant\Goal\Tracker;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Participant\Goal\Tracker
 */
class Controller extends BaseController
{
    use ParticipantServiceTrait;
    use AuthServiceTrait;

    /**
     * @param string $participantId
     * @param string $goalId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function index(string $participantId, string $goalId): Response
    {
        return $this->sendResponse(
            $this->participantService__()
                ->workWith($participantId)
                ->goalService()
                ->workWith($goalId)
                ->trackerService()
                ->list()
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
    public function create(JsonApi $jsonApi, string $participantId, string $goalId): Response
    {
        return $this->sendResponse(
            $this->participantService__()
                ->workWith($participantId)
                ->goalService()->workWith($goalId)->trackerService()->create(
                    $jsonApi->attributes()->toArray()
                )->dto(),
            201
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $participantId
     * @param string  $goalId
     * @param string  $trackerId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function change(JsonApi $jsonApi, string $participantId, string $goalId, string $trackerId): Response
    {
        return $this->sendResponse(
            $this->participantService__()
                ->workWith($participantId)
                ->goalService()
                ->workWith($goalId)
                ->trackerService()
                ->workWith($trackerId)
                ->change(
                    $jsonApi->attributes()->toArray()
                )
                ->dto()
        );
    }

    /**
     * @param string $participantId
     * @param string $goalId
     * @param string $trackerId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function show(string $participantId, string $goalId, string $trackerId): Response
    {
        return $this->sendResponse(
            $this->participantService__()
                ->workWith($participantId)
                ->goalService()
                ->workWith($goalId)
                ->trackerService()
                ->workWith($trackerId)
                ->dto()
        );
    }

    /**
     * @param string $participantId
     * @param string $goalId
     * @param string $trackerId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function remove(string $participantId, string $goalId, string $trackerId): Response
    {
        $this->participantService__()
            ->workWith($participantId)
            ->goalService()
            ->workWith($goalId)
            ->trackerService()
            ->workWith($trackerId)
            ->remove();

        return $this->acknowledgeResponse();
    }
}
