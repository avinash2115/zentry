<?php

namespace App\Components\Users\Http\Controllers\Participant;

use App\Assistants\Elastic\Traits\ElasticFilterConverterTrait;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Users\Http\Controllers\Participant\Traits\BulkProcessingTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Arr;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Participant
 */
class Controller extends BaseController
{
    use ParticipantServiceTrait;
    use AuthServiceTrait;
    use ElasticFilterConverterTrait;
    use ElasticServiceTrait;
    use UserServiceTrait;
    use TeamServiceTrait;
    use BulkProcessingTrait;

    /**
     * @param Request $request
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws NotFoundException
     */
    public function index(Request $request): Response
    {
        $index = $this->elasticService__()::generateIndex(Index::INDEX_FILTERS);

        $requestFilter = collect($request->get('filter', []));
        $requestFilter->put('user_id', $this->authService__()->user()->identity()->toString());

        $elasticFilterConverter = $this->elasticFilterConverter__(
            $requestFilter,
            $index,
            $this->participantService__(),
            $request->get('term')
        );

        $requestFilter = $elasticFilterConverter->filter()->merge($requestFilter);
        $requestFilter->forget(['elastic', 'client_id']);

        $aggregations = $elasticFilterConverter->aggregations();

        $filters = $elasticFilterConverter->filters(
            $aggregations,
            );

        $this->participantService__()->applyFilters($requestFilter->toArray());

        return $this->sendResponse(
            $this->participantService__()->list(),
            200,
            [],
            [
                'filters' => $filters->toArray(),
            ]
        );
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws PermissionDeniedException
     */
    public function create(JsonApi $jsonApi): Response
    {
        $team = null;
        $school = null;

        $teamInput = $jsonApi->relation('team');

        if ($teamInput instanceof JsonApi) {
            $team = $this->teamService__()->workWith($teamInput->id())->readonly();

            $schoolInput = $jsonApi->relation('school');

            if ($schoolInput instanceof JsonApi) {
                $school = $this->teamService__()->schoolService()->workWith($schoolInput->id())->readonly();
            }
        }

        $this->participantService__()->create(
            $this->authService__()->user()->readonly(),
            $jsonApi->attributes()->toArray(),
            $team,
            $school
        );

        $therapyInput = $jsonApi->relation('therapy');

        if ($therapyInput instanceof JsonApi) {
            $this->participantService__()->therapyService()->change($therapyInput->attributes()->toArray());
        }

        $this->createGoals($this->participantService__()->goalService(), $jsonApi);

        return $this->sendResponse($this->participantService__()->dto(), 201);
    }

    /**
     * @param string $participantId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function show(string $participantId): Response
    {
        return $this->sendResponse($this->participantService__()->workWith($participantId)->dto());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $participantId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function change(JsonApi $jsonApi, string $participantId): Response
    {
        $this->participantService__()->workWith($participantId)->change($jsonApi->attributes()->toArray());

        $therapyInput = $jsonApi->relation('therapy');

        if ($therapyInput instanceof JsonApi) {
            $this->participantService__()->therapyService()->change($therapyInput->attributes()->toArray());
        }

        $teamInput = $jsonApi->relation('team');

        if (!$teamInput instanceof JsonApi) {
            $this->participantService__()->change(
                [
                    'team' => null,
                ]
            );
        } else {
            $team = $this->teamService__()->workWith($teamInput->id())->readonly();
            $schoolInput = $jsonApi->relation('school');
            $school = null;

            if ($schoolInput instanceof JsonApi) {
                $school = $this->teamService__()->schoolService()->workWith($schoolInput->id())->readonly();
            }

            $this->participantService__()->change(
                [
                    'team' => $team,
                    'school' => $school,
                ]
            );
        }

        return $this->sendResponse($this->participantService__()->dto());
    }

    /**
     * @param string $participantId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function therapyShow(string $participantId): Response
    {
        return $this->sendResponse($this->participantService__()->workWith($participantId)->therapyService()->dto());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $participantId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function therapyChange(JsonApi $jsonApi, string $participantId): Response
    {
        return $this->sendResponse(
            $this->participantService__()->workWith($participantId)->therapyService()->change(
                $jsonApi->attributes()->toArray()
            )->dto()
        );
    }

    /**
     * @param string $participantId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws PermissionDeniedException
     */
    public function remove(string $participantId): Response
    {
        $this->participantService__()->workWith($participantId)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $participantId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function merge(JsonApi $jsonApi, string $participantId): Response
    {
        $this->participantService__()->workWith($participantId)->merge($jsonApi->attributes()->get('reference'));

        return $this->acknowledgeResponse();
    }
}
