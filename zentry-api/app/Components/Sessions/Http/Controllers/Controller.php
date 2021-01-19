<?php

namespace App\Components\Sessions\Http\Controllers;

use App\Assistants\Elastic\Traits\ElasticFilterConverterTrait;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Filter\Needle;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Paginator;
use App\Assistants\QR\Services\Traits\QRServiceTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Services\Services\Traits\ServiceServiceTrait;
use App\Components\Sessions\Jobs\PostProcess;
use App\Components\Sessions\Services\Poi\Indexable\SetupService;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\SessionDTO;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Share\Services\Shared\Traits\SharedServiceTrait;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Middleware\Access\Shared\Authenticate;
use DateInterval;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Sessions\Http\Controllers
 */
class Controller extends BaseController
{
    use SessionServiceTrait;
    use AuthServiceTrait;
    use QRServiceTrait;
    use DeviceServiceTrait;
    use ParticipantServiceTrait;
    use SharedServiceTrait;
    use ElasticServiceTrait;
    use ElasticFilterConverterTrait;
    use TeamServiceTrait;
    use ServiceServiceTrait;

    /**
     * @param Request $request
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function index(Request $request): Response
    {
        $index = $this->elasticService__()::generateIndex(Index::INDEX_FILTERS);

        $requestFilter = collect($request->get('filter', []));

        $requestFilter->put('user_id', $this->authService__()->user()->identity()->toString());

        $pagination = $requestFilter->get('pagination');
        $requestFilter->forget('pagination');

        $elasticFilterConverter = $this->elasticFilterConverter__(
            $requestFilter,
            $index,
            $this->sessionService__(),
            $request->get('term')
        );

        if ($elasticFilterConverter->shouldProcessRequest()) {
            $filtered = collect(Arr::get($elasticFilterConverter->filter()->toArray(), 'ids.collection'))->map(
                fn(Identity $identity) => $identity->toString()
            )->values();

            if (!strEmpty($request->get('term', ''))) {
                $entitiesIndex = new Index(Index::INDEX_ENTITIES);
                $poiSetup = new SetupService();

                $filtered = $filtered->merge(
                    $this->elasticService__()->filter(
                        $entitiesIndex,
                        $poiSetup,
                        collect(
                            [
                                new Needle(
                                    'user_id',
                                    $this->authService__()->user()->identity()->toString(),
                                    $poiSetup->asMappings($entitiesIndex)
                                ),
                            ]
                        ),
                        app()->make(Paginator::class),
                        $request->get('term')
                    )->map(fn(array $item) => Arr::get($item, 'session_id'))->unique()->filter()
                );
            }

            if ($filtered->isNotEmpty()) {
                $requestFilter->put('_id', $filtered->unique()->values()->toArray());
            }

            if ($pagination !== null) {
                $requestFilter->put('pagination', $pagination);
            }

            $elasticFilterConverter = $this->elasticFilterConverter__(
                $requestFilter,
                $index,
                $this->sessionService__(),
                );

            $filter = $elasticFilterConverter->filter();

            $filter->forget('user_id');

            $filters = $elasticFilterConverter->filters(
                $elasticFilterConverter->aggregations(),
                collect(
                    [
                        'user_id' => true,
                        'status' => true,
                    ]
                )
            );
        } else {
            $filter = $requestFilter;
            $filters = collect();
        }

        $this->sessionService__()->applyFilters($filter->toArray());

        return $this->sendResponse($this->sessionService__()->list(), 200, [], ['filters' => $filters->toArray()]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function scheduled(Request $request): Response
    {
        $filter = [
            'scheduled_on' => [
                'scheduled' => false,
            ],
        ];

        $passedFilter = $request->get('filter');

        if (Arr::has($passedFilter, 'scheduled_on.range')) {
            $filter['scheduled_on']['range'] = Arr::get($passedFilter, 'scheduled_on.range');
        }

        $this->sessionService__()->applyFilters($filter);

        return $this->sendResponse($this->sessionService__()->list());
    }

    /**
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NonUniqueResultException|NotFoundException|PropertyNotInit|UnexpectedValueException
     * @throws RuntimeException
     */
    public function active(): Response
    {
        return $this->sendResponse($this->sessionService__()->workWithActive()->dto());
    }

    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws NonUniqueResultException
     * @throws RuntimeException
     */
    public function dead(): Response
    {
        return $this->sendResponse($this->sessionService__()->workWithDead()->dto());
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NonUniqueResultException|NotFoundException|PropertyNotInit|UnexpectedValueException|RuntimeException
     */
    public function adhoc(JsonApi $jsonApi): Response
    {
        return $this->sendResponse(
            $this->create($jsonApi)->start()->dto()
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
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function schedule(JsonApi $jsonApi): Response
    {
        if ($jsonApi->attributes()->get('scheduled_on') === null) {
            throw new InvalidArgumentException('scheduled_on is missed.');
        }

        if ($jsonApi->attributes()->get('scheduled_to') === null) {
            throw new InvalidArgumentException('scheduled_to is missed.');
        }

        return $this->sendResponse(
            $this->create($jsonApi)->dto()
        );
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @return SessionServiceContract
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    protected function create(JsonApi $jsonApi): SessionServiceContract
    {
        $attributes = $jsonApi->attributes()->toArray();

        $service = $jsonApi->relation('service');

        if ($service instanceof JsonApi) {
            Arr::set(
                $attributes,
                'service',
                $this->serviceService__()->workWith(
                    $service->id()
                )->readonly()
            );
        }

        $school = $jsonApi->relation('school');
        $team = $jsonApi->relation('team');

        if ($team instanceof JsonApi && $school instanceof JsonApi) {
            Arr::set(
                $attributes,
                'school',
                $this->teamService__()->workWith(
                    $team->id()
                )->schoolService()->workWith(
                    $school->id()
                )->readonly()
            );
        }

        $this->sessionService__()->create(
            $this->authService__()->user()->readonly(),
            $attributes
        );

        $jsonApi->relations('participants')->each(
            function (JsonApi $jsonApi) {
                $this->addParticipant($jsonApi);
            }
        );

        $datetime = new DateTime();

        $jsonApi->relations('goals')->each(
            function (JsonApi $jsonApi) use (&$datetime) {
                $this->addGoals($jsonApi, $datetime);
                $datetime = clone $datetime;
                $datetime->add(new DateInterval('PT1S'));
            }
        );

        return $this->sessionService__();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $id
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NonUniqueResultException|NotFoundException|PropertyNotInit|UnexpectedValueException|RuntimeException
     */
    public function change(JsonApi $jsonApi, string $id): Response
    {
        $this->sessionService__()->workWith($id);

        if ($jsonApi->relationships()->has('participants')) {
            $participants = $this->sessionService__()->readonly()->participants()->keyBy(
                static fn(ParticipantReadonlyContract $participant) => $participant->identity()->toString()
            );

            $participantsInput = $jsonApi->relations('participants')->keyBy(
                static fn(JsonApi $jsonApi) => $jsonApi->id()
            );

            $participantsInput->diffKeys($participants)->each(
                function (JsonApi $jsonApi) {
                    $this->addParticipant($jsonApi);
                }
            );

            $participants->diffKeys($participantsInput)->each(
                function (ParticipantReadonlyContract $participant) {
                    $this->sessionService__()->audienceService()->kick($participant);
                }
            );
        }

        $attributes = $jsonApi->attributes()->toArray();

        $service = $jsonApi->relation('service');

        if ($service instanceof JsonApi) {
            Arr::set(
                $attributes,
                'service',
                $this->serviceService__()->workWith(
                    $service->id()
                )->readonly()
            );
        }

        $school = $jsonApi->relation('school');
        $team = $jsonApi->relation('team');

        if ($team instanceof JsonApi && $school instanceof JsonApi) {
            Arr::set(
                $attributes,
                'school',
                $this->teamService__()->workWith(
                    $team->id()
                )->schoolService()->workWith(
                    $school->id()
                )->readonly()
            );
        }

        return $this->sendResponse(
            $this->sessionService__()->change($attributes)->dto()
        );
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     * @throws RuntimeException
     */
    public function show(Request $request, string $id): Response
    {
        return $this->sendResponse($this->filter($request, $this->sessionService__()->workWith($id)->dto()));
    }

    /**
     * @param Request    $request
     * @param SessionDTO $session
     *
     * @return SessionDTO
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    private function filter(Request $request, SessionDTO $session): SessionDTO
    {
        $id = $request->header(Authenticate::HEADER, '');

        if (is_string($id) && IdentityGenerator::isValid($id)) {
            $shared = $this->sharedService__()->workWith($id)->readonly();
            if ($shared->type() === Mutator::TYPE) {
                $session->streams = collect();

                $session->pois = $session->pois->filter(
                    static function (PoiDTO $poi) use ($shared) {
                        return in_array($poi->id(), $shared->payload()->parameters(), true);
                    }
                );
            }
        }

        return $session;
    }

    /**
     * @param string $id
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function start(string $id): Response
    {
        if (!$this->sessionService__()->workWith($id)->readonly()->isScheduled()) {
            throw new RuntimeException('This session is not scheduled. Start is not available.');
        }

        return $this->sendResponse(
            $this->sessionService__()->start()->dto()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $id
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NonUniqueResultException|NotFoundException|PropertyNotInit|UnexpectedValueException|RuntimeException
     */
    public function end(JsonApi $jsonApi, string $id): Response
    {
        return $this->sendResponse($this->sessionService__()->workWith($id)->end()->dto());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $id
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function remove(JsonApi $jsonApi, string $id): Response
    {
        $this->sessionService__()->workWith($id)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $id
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NonUniqueResultException|NotFoundException|PropertyNotInit|UnexpectedValueException|RuntimeException
     */
    public function wrap(JsonApi $jsonApi, string $id): Response
    {
        return $this->sendResponse($this->sessionService__()->workWith($id)->wrap()->dto());
    }

    /**
     * @param Request $request
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|NotFoundException|PropertyNotInit|InvalidArgumentException|UnexpectedValueException
     */
    public function qr(Request $request, string $sessionId): Response
    {
        $payload = $this->sessionService__()->workWith($sessionId)->asQRPayload();

        return app(ResponseFactory::class)->make($this->QRService__()->render($payload))->withHeaders(
            ['Content-Type' => 'image/svg+xml']
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function postProcess(JsonApi $jsonApi, string $sessionId): Response
    {
        dispatch(new PostProcess(new Identity($sessionId)));

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    public function addParticipants(JsonApi $jsonApi, string $sessionId): Response
    {
        $this->sessionService__()->workWith($sessionId);

        $jsonApi->asJsonApiCollection()->each(
            function (JsonApi $jsonApi) {
                $this->addParticipant($jsonApi);
            }
        );

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    private function addParticipant(JsonApi $jsonApi): void
    {
        $this->sessionService__()->audienceService()->add(
            $this->participantService__()->workWith($jsonApi->id())->readonly()
        );
    }

    /**
     * @param JsonApi  $jsonApi
     * @param DateTime $dateTime
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    private function addGoals(JsonApi $jsonApi, DateTime $dateTime): void
    {
        $participantInput = $jsonApi->relation('participant');
        $goalInput = $jsonApi->relation('goal');

        if (!$participantInput instanceof JsonApi) {
            throw new InvalidArgumentException('Participant is missed');
        }

        if (!$goalInput instanceof JsonApi) {
            throw new InvalidArgumentException('goal is missed');
        }

        $this->sessionService__()->goalService()->create(
            $this->participantService__()->workWith($participantInput->id())->readonly(),
            $this->participantService__()->goalService()->workWith($goalInput->id())->readonly(),
            $dateTime
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    public function removeParticipants(JsonApi $jsonApi, string $sessionId): Response
    {
        $this->sessionService__()->workWith($sessionId);

        $jsonApi->asJsonApiCollection()->each(
            function (JsonApi $jsonApi) {
                $this->sessionService__()->audienceService()->kick(
                    $this->participantService__()->workWith($jsonApi->id())->readonly()
                );
            }
        );

        return $this->acknowledgeResponse();
    }

    /**
     * @param string $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function share(string $sessionId): Response
    {
        return $this->sendResponse(
            $this->sharedService__()->create($this->sessionService__()->workWith($sessionId))->dto()
        );
    }

    /**
     * @param string $sessionId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function unshare(string $sessionId): Response
    {
        try {
            $this->sharedService__()->workWithSharable($this->sessionService__()->workWith($sessionId))->remove();
        } catch (NotFoundException $exception) {
            report($exception);
        }

        return $this->acknowledgeResponse();
    }
}
