<?php

namespace App\Components\Users\Http\Controllers\Participant\IEP;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Http\Controllers\Participant\Traits\BulkProcessingTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Participant\IEP
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
            $this->participantService__()->workWith($participantId)->IEPService()->list()
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
        $this->participantService__()->workWith($participantId)->IEPService()->create(
            $jsonApi->attributes()->toArray()
        );

        return $this->sendResponse(
            $this->participantService__()->IEPService()->dto(),
            201
        );
    }

    /**
     * @param string $participantId
     * @param string $iepId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function show(string $participantId, string $iepId): Response
    {
        return $this->sendResponse(
            $this->participantService__()->workWith($participantId)->IEPService()->workWith($iepId)->dto()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $participantId
     * @param string  $iepId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function change(JsonApi $jsonApi, string $participantId, string $iepId): Response
    {
        $this->participantService__()->workWith($participantId)->IEPService()->workWith($iepId);

        return $this->sendResponse(
            $this->participantService__()->IEPService()->change(
                $jsonApi->attributes()->toArray()
            )->dto()
        );
    }

    /**
     * @param string $participantId
     * @param string $iepId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function remove(string $participantId, string $iepId): Response
    {
        $this->participantService__()->workWith($participantId)->IEPService()->workWith($iepId)->remove();

        return $this->acknowledgeResponse();
    }
}
