<?php

namespace App\Components\Sessions\Http\Controllers\Note;

use App\Assistants\Files\Http\Controllers\Traits\FileReceiverTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Services\Note\NoteServiceContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Note\Mutators\DTO\Mutator;
use App\Components\Sessions\ValueObjects\Note\Payload;
use App\Components\Sessions\ValueObjects\Upload\Progress;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Http\Controllers\Controller as BaseController;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Kladislav\LaravelChunkUpload\Exceptions\UploadMissingFileException;
use Kladislav\LaravelChunkUpload\Receiver\FileReceiver;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Sessions\Http\Controllers\Note
 */
class Controller extends BaseController
{
    use SessionServiceTrait;
    use ParticipantServiceTrait;
    use FileReceiverTrait;

    /**
     * @param string $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     */
    public function index(string $sessionId): Response
    {
        return $this->sendResponse(
            $this->_noteService($sessionId)->list()
        );
    }

    /**
     * @param Request $request
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws NonUniqueResultException
     */
    public function create(Request $request, JsonApi $jsonApi, string $sessionId): Response
    {
        return $this->sendResponse($this->_noteService($sessionId)->create($this->payload($request, $jsonApi))->dto(), 201);
    }

    /**
     * @param Request $request
     * @param JsonApi $jsonApi
     *
     * @return Payload
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    private function payload(Request $request, ?JsonApi $jsonApi = null): Payload
    {
        $text = null;
        $participant = null;
        $poi = null;
        $poiParticipant = null;

        if ($jsonApi instanceof JsonApi) {
            $text = $jsonApi->attributes()->get('text');

            if ($jsonApi->relation('participant') instanceof $jsonApi) {
                $participant = $this->participantService__()->workWith($jsonApi->relation('participant')->id())->readonly();
            }

            if ($jsonApi->relation('poi') instanceof $jsonApi) {
                $poi = $this->sessionService__()->poiService()->workWith($jsonApi->relation('poi')->id())->readonly();

                if ($jsonApi->relation('poi_participant') instanceof $jsonApi) {
                    $poiParticipant = $this->sessionService__()->poiService()->participantService()->workWith(
                        $jsonApi->relation('poi_participant')->id()
                    )->readonly();
                }
            }
        } else {
            switch (true) {
                case $request->has('participant_id'):
                    if (IdentityGenerator::isValid((string)$request->get('participant_id'))) {
                        $participant = $this->participantService__()->workWith($request->get('participant_id'))->readonly();
                    }
                break;
                case $request->has('poi_id'):
                    if (IdentityGenerator::isValid((string)$request->get('poi_id'))) {
                        $poi = $this->sessionService__()->poiService()->workWith($request->get('poi_id'))->readonly();

                        if (IdentityGenerator::isValid((string)$request->get('poi_participant_id'))) {
                            $poiParticipant = $this->sessionService__()->poiService()->participantService()->workWith(
                                $request->get('poi_participant_id')
                            )->readonly();
                        }
                    }
                break;
            }
        }

        return new Payload(
            $text, null, $participant, $poi, $poiParticipant
        );
    }

    /**
     * @param FileReceiver $receiver
     * @param Request      $request
     * @param string       $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws UploadMissingFileException
     */
    public function upload(FileReceiver $receiver, Request $request, string $sessionId): Response
    {
        $save = $this->receive($receiver);

        if ($save->isFinished()) {
            $noteService = $this->_noteService($sessionId);

            $noteService->create($this->payload($request), $save->getFile());

            @unlink($save->getFile()->getPathname());

            return $this->sendResponse($noteService->dto(), 201);
        }

        return $this->sendResponse(new Progress($save->handler()->getPercentageDone(), Mutator::TYPE_PROGRESS));
    }

    /**
     * @param string $sessionId
     * @param string $noteId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     * @throws RuntimeException
     */
    public function show(string $sessionId, string $noteId): Response
    {
        return $this->sendResponse(
            $this->_noteService($sessionId, $noteId)->dto()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     * @param string  $noteId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function change(JsonApi $jsonApi, string $sessionId, string $noteId): Response
    {
        return $this->sendResponse(
            $this->_noteService($sessionId, $noteId)->change($jsonApi->attributes()->toArray())->dto()
        );
    }

    /**
     * @param FileReceiver $receiver
     * @param string       $sessionId
     * @param string       $noteId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws UploadMissingFileException
     */
    public function reupload(FileReceiver $receiver, string $sessionId, string $noteId): Response
    {
        $save = $this->receive($receiver);

        if ($save->isFinished()) {
            $noteService = $this->_noteService($sessionId);

            $noteService->workWith($noteId)->change(
                [
                    'file' => $save->getFile(),
                ]
            );

            @unlink($save->getFile()->getPathname());

            return $this->sendResponse($noteService->dto(), 201);
        }

        return $this->sendResponse(new Progress($save->handler()->getPercentageDone(), Mutator::TYPE_PROGRESS));
    }

    /**
     * @param string $sessionId
     * @param string $noteId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     */
    public function remove(string $sessionId, string $noteId): Response
    {
        $this->_noteService($sessionId, $noteId)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @param string      $sessionId
     * @param string|null $noteId
     *
     * @return NoteServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    private function _noteService(string $sessionId, string $noteId = null): NoteServiceContract
    {
        $this->sessionService__()->workWith($sessionId);

        if ($noteId !== null) {
            $this->sessionService__()->noteService()->workWith($noteId);
        }

        return $this->sessionService__()->noteService();
    }
}
