<?php

namespace App\Components\Sessions\Http\Controllers\SOAP;

use App\Assistants\Files\Http\Controllers\Traits\FileReceiverTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Services\SOAP\SOAPServiceContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\ValueObjects\SOAP\Payload;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Http\Controllers\Controller as BaseController;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
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
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function index(string $sessionId): Response
    {
        return $this->sendResponse($this->_soapService($sessionId)->list());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     */
    public function create(JsonApi $jsonApi, string $sessionId): Response
    {
        return $this->sendResponse($this->_soapService($sessionId)->create($this->payload($jsonApi))->dto(), 201);
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     */
    public function createBulk(JsonApi $jsonApi, string $sessionId): Response
    {
        $jsonApi->asJsonApiCollection()->each(
            function (JsonApi $jsonApi) use ($sessionId) {
                try {
                    if (!IdentityGenerator::isValid($jsonApi->id())) {
                        throw new RuntimeException();
                    }

                    $this->change($jsonApi, $sessionId, $jsonApi->id());
                } catch (NotFoundException|RuntimeException $exception) {
                    $this->create($jsonApi, $sessionId);
                }
            }
        );

        return $this->acknowledgeResponse(true, 201);
    }

    /**
     * @param string $sessionId
     * @param string $soapId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function show(string $sessionId, string $soapId): Response
    {
        return $this->sendResponse($this->_soapService($sessionId, $soapId)->dto());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     * @param string  $soapId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function change(JsonApi $jsonApi, string $sessionId, string $soapId): Response
    {
        return $this->sendResponse(
            $this->_soapService($sessionId, $soapId)->change($jsonApi->attributes()->toArray())->dto()
        );
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
        $this->_soapService($sessionId, $noteId)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @param string      $sessionId
     * @param string|null $soapId
     *
     * @return SOAPServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    private function _soapService(string $sessionId, string $soapId = null): SOAPServiceContract
    {
        $this->sessionService__()->workWith($sessionId);

        if ($soapId !== null) {
            $this->sessionService__()->SOAPService()->workWith($soapId);
        }

        return $this->sessionService__()->SOAPService();
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @return Payload
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    private function payload(JsonApi $jsonApi): Payload
    {
        if (!$jsonApi->relation('participant') instanceof JsonApi) {
            throw new InvalidArgumentException('Participant is required');
        }

        $this->participantService__()->workWith($jsonApi->relation('participant')->id());

        if ($jsonApi->relation('goal') instanceof JsonApi && IdentityGenerator::isValid($jsonApi->relation('goal')->id())) {
            $this->participantService__()->goalService()->workWith($jsonApi->relation('goal')->id());
        }

        try {
            $goal = $this->participantService__()->goalService()->readonly();
        } catch (PropertyNotInit $exception) {
            $goal = null;
        }

        return new Payload(
            $jsonApi->attributes()->get('present', false),
            (string)$jsonApi->attributes()->get('rate'),
            (string)$jsonApi->attributes()->get('activity'),
            (string)$jsonApi->attributes()->get('note'),
            (string)$jsonApi->attributes()->get('plan'),
            $this->participantService__()->readonly(),
            $goal,
        );
    }
}
