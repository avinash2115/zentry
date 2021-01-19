<?php

namespace App\Components\Sessions\Http\Controllers\Progress;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Services\Progress\ProgressServiceContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\ValueObjects\Progress\Payload;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Http\Controllers\Controller as BaseController;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Sessions\Http\Controllers\Progress
 */
class Controller extends BaseController
{
    use SessionServiceTrait;
    use ParticipantServiceTrait;

    /**
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     */
    public function index(string $sessionId): Response
    {
        return $this->sendResponse(
            $this->_progressService($sessionId)->list()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException|RuntimeException
     * @throws Exception
     */
    public function create(JsonApi $jsonApi, string $sessionId): Response
    {
        $participantInput = $jsonApi->relation('participant');
        $goalInput = $jsonApi->relation('goal');
        $trackerInput = $jsonApi->relation('tracker');
        $poiInput = $jsonApi->relation('poi');

        if (!$participantInput instanceof JsonApi) {
            throw new InvalidArgumentException('Participant is required for progress creation');
        }

        if (!$goalInput instanceof JsonApi) {
            throw new InvalidArgumentException('Goal is required for progress creation');
        }

        if (!$trackerInput instanceof JsonApi) {
            throw new InvalidArgumentException('Tracker is required for progress creation');
        }

        return $this->sendResponse(
            $this->_progressService($sessionId)->create(
                new Payload(
                    IdentityGenerator::next(),
                    new DateTime($jsonApi->attributes()->get('datetime')),
                    $this->participantService__()->workWith($participantInput->id())->readonly(),
                    $this->participantService__()->goalService()->workWith($goalInput->id())->readonly(),
                    $this->participantService__()
                        ->goalService()
                        ->trackerService()
                        ->workWith($trackerInput->id())
                        ->readonly(),
                ),
                $poiInput instanceof JsonApi ? $this->sessionService__()
                    ->poiService()
                    ->workWith($poiInput->id())
                    ->readonly() : null
            )->dto(),
            201
        );
    }

    /**
     * @param string $sessionId
     * @param string $progressId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     */
    public function show(string $sessionId, string $progressId): Response
    {
        return $this->sendResponse(
            $this->_progressService($sessionId, $progressId)->dto()
        );
    }

    /**
     * @param string $sessionId
     * @param string $progressId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     */
    public function remove(string $sessionId, string $progressId): Response
    {
        $this->_progressService($sessionId, $progressId)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @param string      $sessionId
     * @param string|null $progressId
     *
     * @return ProgressServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    private function _progressService(string $sessionId, string $progressId = null): ProgressServiceContract
    {
        $this->sessionService__()->workWith($sessionId);

        if ($progressId !== null) {
            $this->sessionService__()->progressService()->workWith($progressId);
        }

        return $this->sessionService__()->progressService();
    }
}
