<?php

namespace App\Components\Sessions\Services;

use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Traits\IndexableTrait;
use App\Assistants\Elastic\ValueObjects\Body;
use App\Assistants\Elastic\ValueObjects\Document;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mapping;
use App\Assistants\Elastic\ValueObjects\Mappings;
use App\Assistants\Elastic\ValueObjects\Type;
use App\Assistants\Events\EventRegistry;
use App\Assistants\Files\Services\Traits\HasFilesTrait;
use App\Assistants\QR\ValueObjects\Payload;
use App\Assistants\QR\ValueObjects\Url;
use App\Assistants\QR\ValueObjects\UrlPayload;
use App\Assistants\Search\Agency\Services\Traits\SearchableTrait;
use App\Components\Sessions\Exceptions\ActiveException;
use App\Components\Sessions\Jobs\PostProcess;
use App\Components\Sessions\Services\SOAP\SOAPServiceContract;
use App\Components\Sessions\Session\Events\Broadcast\Created;
use App\Components\Sessions\Services\Note\NoteServiceContract;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator as PoiMutator;
use App\Components\Sessions\Services\Goal\GoalServiceContract;
use App\Components\Sessions\Services\Poi\PoiServiceContract;
use App\Components\Sessions\Services\Progress\ProgressServiceContract;
use App\Components\Sessions\Services\Stream\StreamServiceContract;
use App\Components\Sessions\Services\Traits\SessionHelperTrait;
use App\Components\Sessions\Session\Events\Broadcast\Changed;
use App\Components\Sessions\Session\Events\Broadcast\Ended;
use App\Components\Sessions\Session\Events\Broadcast\Participant\Added;
use App\Components\Sessions\Session\Events\Broadcast\Participant\Removed;
use App\Components\Sessions\Session\Events\Broadcast\Started;
use App\Components\Sessions\Session\Events\Broadcast\Wrapped;
use App\Components\Sessions\Session\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\Repository\SessionRepositoryContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\SessionDTO;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Share\Services\Shared\Traits\SharedServiceTrait;
use App\Components\Share\Shared\SharedReadonlyContract;
use App\Components\Share\ValueObjects\Payload as SharePayload;
use App\Components\Users\Participant\ParticipantDTO;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\Services\Participant\Audience\Traits\AudiencableServiceTrait;
use App\Components\Users\Services\Participant\ParticipantServiceContract;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Components\Users\ValueObjects\Device\ConnectingToken;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\Services\Traits\GuardedTrait;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Tag;
use App\Convention\ValueObjects\Tags;
use Cache;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class SessionService
 *
 * @package App\Components\Sessions\Services
 */
class SessionService implements SessionServiceContract
{
    use GuardedTrait;
    use FilterableTrait;
    use DeviceServiceTrait;
    use HasFilesTrait;
    use SessionHelperTrait;
    use AudiencableServiceTrait;
    use SharedServiceTrait;
    use IndexableTrait;
    use SearchableTrait;
    use UserServiceTrait;
    use TeamServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var SessionContract | null
     */
    private ?SessionContract $entity = null;

    /**
     * @var SessionRepositoryContract | null
     */
    private ?SessionRepositoryContract $repository = null;

    /**
     * @var PoiServiceContract|null
     */
    private ?PoiServiceContract $poiService = null;

    /**
     * @var StreamServiceContract|null
     */
    private ?StreamServiceContract $streamService = null;

    /**
     * @var ProgressServiceContract|null
     */
    private ?ProgressServiceContract $progressService = null;

    /**
     * @var GoalServiceContract|null
     */
    private ?GoalServiceContract $goalService = null;

    /**
     * @var NoteServiceContract|null
     */
    private ?NoteServiceContract $noteService = null;

    /**
     * @var SOAPServiceContract|null
     */
    private ?SOAPServiceContract $SOAPService = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function _mutator(): Mutator
    {
        $this->setMutator();

        return $this->mutator;
    }

    /**
     * @return self
     * @throws BindingResolutionException
     */
    private function setMutator(): self
    {
        if (!$this->mutator instanceof Mutator) {
            $this->mutator = app()->make(Mutator::class);
        }

        return $this;
    }

    /**
     * @return SessionRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): SessionRepositoryContract
    {
        $this->setRepository();

        return $this->repository;
    }

    /**
     * @return self
     * @throws BindingResolutionException
     */
    private function setRepository(): self
    {
        if (!$this->repository instanceof SessionRepositoryContract) {
            $this->repository = app()->make(SessionRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    private function setGoalService(): SessionServiceContract
    {
        $this->goalService = app()->make(
            GoalServiceContract::class,
            [
                'session' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setNoteService(): SessionServiceContract
    {
        $this->noteService = app()->make(
            NoteServiceContract::class,
            [
                'sessionService' => $this,
                'session' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setPoiService(): SessionServiceContract
    {
        $this->poiService = app()->make(
            PoiServiceContract::class,
            [
                'sessionService' => $this,
                'session' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    private function setProgressService(): SessionServiceContract
    {
        $this->progressService = app()->make(
            ProgressServiceContract::class,
            [
                'session' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setSOAPService(): SessionServiceContract
    {
        $this->SOAPService = app()->make(
            SOAPServiceContract::class,
            [
                'sessionService' => $this,
                'session' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    private function setStreamService(): SessionServiceContract
    {
        $this->streamService = app()->make(
            StreamServiceContract::class,
            [
                'sessionService' => $this,
                'session' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function poiService(): PoiServiceContract
    {
        if (!$this->poiService instanceof PoiServiceContract) {
            $this->setPoiService();
        }

        return $this->poiService;
    }

    /**
     * @inheritDoc
     */
    public function streamService(): StreamServiceContract
    {
        if (!$this->streamService instanceof StreamServiceContract) {
            $this->setStreamService();
        }

        return $this->streamService;
    }

    /**
     * @inheritDoc
     */
    public function noteService(): NoteServiceContract
    {
        if (!$this->noteService instanceof NoteServiceContract) {
            $this->setNoteService();
        }

        return $this->noteService;
    }

    /**
     * @inheritDoc
     */
    public function SOAPService(): SOAPServiceContract
    {
        if (!$this->SOAPService instanceof SOAPServiceContract) {
            $this->setSOAPService();
        }

        return $this->SOAPService;
    }

    /**
     * @inheritDoc
     */
    public function progressService(): ProgressServiceContract
    {
        if (!$this->progressService instanceof ProgressServiceContract) {
            $this->setProgressService();
        }

        return $this->progressService;
    }

    /**
     * @inheritDoc
     */
    public function goalService(): GoalServiceContract
    {
        if (!$this->goalService instanceof GoalServiceContract) {
            $this->setGoalService();
        }

        return $this->goalService;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): SessionServiceContract
    {
        $this->applyFilters([]);

        $this->setEntity($this->_repository()->byIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWithActive(): SessionServiceContract
    {
        $this->applyFilters([]);

        $this->guardRepository(
            $this,
            function (UserReadonlyContract $user) {
                $this->_repository()->filterByUsersIds([$user->identity()]);
            },
            function () { }
        );

        $entity = $this->_repository()->filterByStatuses([SessionReadonlyContract::STATUS_STARTED])->getOne();

        if (!$entity instanceof SessionContract) {
            throw new NotFoundException('No active session was found');
        }

        $this->setEntity($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWithDead(): SessionServiceContract
    {
        $this->applyFilters([]);

        $this->guardRepository(
            $this,
            function (UserReadonlyContract $user) {
                $this->_repository()->filterByUsersIds([$user->identity()]);
            },
            function () { }
        );

        $entity = $this->_repository()->filterByStatuses([SessionReadonlyContract::STATUS_ENDED])->getOne();

        if (!$entity instanceof SessionContract || !$entity->isDead()) {
            throw new NotFoundException('No dead session was found');
        }

        $this->setEntity($entity);

        return $this;
    }

    /**
     * @return SessionContract
     * @throws PropertyNotInit
     */
    private function _entity(): SessionContract
    {
        if (!$this->entity instanceof SessionContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param SessionContract $entity
     *
     * @return SessionServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws PermissionDeniedException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws NotImplementedException
     */
    private function setEntity(SessionContract $entity): SessionServiceContract
    {
        $this->entity = $entity;

        $this->guardEntity(
            $this,
            function (UserReadonlyContract $user) {
                return $this->_entity()->user()->identity()->equals($user->identity());
            }
        );

        $this->setPoiService();
        $this->setStreamService();
        $this->setProgressService();
        $this->setGoalService();
        $this->setAudienceService();
        $this->setNoteService();
        $this->setSOAPService();
        $this->setAudienceService();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function identity(): Identity
    {
        return $this->_entity()->identity();
    }

    /**
     * @inheritDoc
     */
    public function readonly(): SessionReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): SessionDTO
    {
        $dto = $this->_mutator()->toDTO($this->_entity());

        if ($this->_entity()->isWrapped()) {
            try {
                $this->sharedService__()->workWithSharable($this);
                $dto->isShared = true;
            } catch (NotFoundException $exception) {
            }

            $dto->pois->each(
                function (PoiDTO $dto) {
                    try {
                        $this->sharedService__()->workWithSharable($this->poiService()->workWith($dto->id));
                        $dto->isShared = true;
                    } catch (NotFoundException $exception) {
                    }
                }
            );
        }

        return $dto;
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (SessionReadonlyContract $entity) {
                $this->workWith($entity->identity());

                $dto = $this->_mutator()->toDTO($entity);

                try {
                    $this->sharedService__()->workWithSharable($this);
                    $dto->isShared = true;
                } catch (NotFoundException $exception) {
                }

                return $dto;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->guardRepository(
            $this,
            function (UserReadonlyContract $user) {
                $this->_repository()->filterByUsersIds([$user->identity()]);
            },
            function (SharedReadonlyContract $shared) {
                $this->_repository()->filterByIds([Arr::get($shared->payload()->parameters(), 'sessionId', '')]);
            }
        );

        $this->handleFilters($this->filters());
        $this->applyFilters([]);

        return $this->_repository()->getAll();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $this->guardRepository(
            $this,
            function (UserReadonlyContract $user) {
                $this->_repository()->filterByUsersIds([$user->identity()]);
            },
            function (SharedReadonlyContract $shared) {
                $this->_repository()->filterByIds([Arr::get($shared->payload()->parameters(), 'sessionId', '')]);
            }
        );

        $this->handleFilters($this->filters());
        $this->applyFilters([]);

        return $this->_repository()->count();
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): SessionServiceContract
    {
        if (Arr::has($data, 'name')) {
            $this->_entity()->changeName(Arr::get($data, 'name', ''));
        }

        if (Arr::has($data, 'type')) {
            $this->_entity()->changeType(Arr::get($data, 'type', ''));
        }

        if (Arr::has($data, 'description')) {
            $this->_entity()->changeDescription(Arr::get($data, 'description', ''));
        }

        if (Arr::has($data, 'geo')) {
            $this->_entity()->changeGeo($this->makeGeo(Arr::get($data, 'geo', [])));
        }

        if (Arr::has($data, 'tags')) {
            $this->_entity()->changeTags($this->makeTags(Arr::get($data, 'tags', [])));
        }

        if (Arr::has($data, 'thumbnail')) {
            $this->_entity()->changeThumbnail(Arr::get($data, 'thumbnail', ''));
        }

        if (Arr::has($data, 'service')) {
            $this->_entity()->changeService(Arr::get($data, 'service'));
        }

        if (Arr::has($data, 'school')) {
            $this->_entity()->changeSchool(Arr::get($data, 'school'));
        }

        if (Arr::has($data, 'scheduled_on') && $this->_entity()->isStatus(SessionReadonlyContract::STATUS_NEW)) {
            $scheduledOnInput = Arr::get($data, 'scheduled_on');

            if ($scheduledOnInput !== null) {
                $this->_entity()->changeScheduledOn(toUTC(new DateTime($scheduledOnInput)));
            }
        }

        if (Arr::has($data, 'scheduled_to') && $this->_entity()->isStatus(SessionReadonlyContract::STATUS_NEW)) {
            $scheduledToInput = Arr::get($data, 'scheduled_to');

            if ($scheduledToInput !== null) {
                $this->_entity()->changeScheduledTo(toUTC(new DateTime($scheduledToInput)));
            }
        }

        if (Arr::has($data, 'sign')) {
            $this->_entity()->changeSign(Arr::get($data, 'sign'));
        }

        if (Arr::has($data, 'excluded_goals')) {
            $this->_entity()->changeExcludedGoals(Arr::get($data, 'excluded_goals', []));
        }

        if ($this->_entity()->isDirty()) {
            app()->make(EventRegistry::class)->registerBroadcast(
                new Changed($this->dto(), $this->_entity()->user()->identity())
            );
        }

        $this->stateChanged();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function touch(): SessionServiceContract
    {
        $this->_entity()->touch();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(UserReadonlyContract $user, array $data): SessionServiceContract
    {
        $entity = $this->make($user, $data);

        $this->setEntity($entity);

        $this->_repository()->persist($entity);

        $this->change(Arr::only($data, ['service', 'school']));

        $this->stateChanged();

        app()->make(EventRegistry::class)->registerBroadcast(
            new Created($this->dto(), $this->_entity()->user()->identity())
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function start(): SessionServiceContract
    {
        $entity = $this->_entity();

        try {
            $this->workWithActive();

            if (!$entity->identity()->equals($this->_entity()->identity())) {
                throw new ActiveException();
            }
        } catch (NotFoundException $exception) {
            $this->workWith($entity->identity());
        }

        if (!$this->_entity()->isStarted()) {
            $this->_entity()->start();

            app()->make(EventRegistry::class)->registerBroadcast(
                new Started($this->dto(), $this->_entity()->user()->identity())
            );
        }

        $this->stateChanged();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function end(): SessionServiceContract
    {
        if ($this->_entity()->isEnded()) {
            return $this;
        }

        $this->_entity()->end();

        $newDevices = collect(Cache::get($this->_entity()->identity()->toString()))->filter(
            function (ConnectingPayload $payload) {
                $this->deviceService__()->applyFilters(
                    [
                        'references' => [
                            'collection' => [$payload->reference()],
                        ],
                    ]
                );

                if ($this->deviceService__()->count()) {
                    Cache::forget($payload->reference());

                    return false;
                }

                Cache::set(
                    $payload->reference(),
                    new ConnectingToken($this->_entity()->user()->identity(), new DateTime()),
                    self::SAVE_DEVICE_TTL
                );

                return true;
            }
        );

        Cache::set(
            $this->_entity()->identity()->toString(),
            $newDevices,
            self::SAVE_DEVICE_TTL
        );

        app()->make(EventRegistry::class)->registerBroadcast(
            new Ended($this->dto(), $this->_entity()->user()->identity())
        );

        $this->stateChanged();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): SessionServiceContract
    {
        if ($this->_entity()->isStarted()) {
            $this->end();
        }

        $this->_repository()->destroy($this->_entity());

        $this->stateDeletion();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function wrap(bool $storeEntire = true): SessionServiceContract
    {
        if ($this->_entity()->isWrapped()) {
            return $this;
        }

        $this->isWrapAllowed();

        app()->make(EventRegistry::class)->registerBroadcast(
            new Wrapped($this->dto(), $this->_entity()->user()->identity())
        );

        if (!app()->runningUnitTests()) {
            dispatch(new PostProcess($this->_entity()->identity(), $storeEntire));
        }

        $this->_entity()->wrap();

        $this->stateChanged();

        try {
            $this->exportToCRM();
        } catch (Exception $exception) {
            report($exception);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function asQRPayload(): Payload
    {
        return new UrlPayload(
            new Url(route(self::ROUTE_CONNECT_DEVICE, ['sessionId' => $this->_entity()->identity()->toString()]))
        );
    }

    /**
     * @inheritDoc
     */
    public function fileNamespaceParts(bool $humanReadable = false): array
    {
        if ($humanReadable) {
            return [
                env('APP_NAME'),
                $this->readonly()->name(),
            ];
        }

        return [
            $this->_entity()->user()->fileNamespace(),
            $this->readonly()->identity()->toString(),
            'streams',
        ];
    }

    /**
     * @inheritDoc
     */
    public function participantAdded(ParticipantDTO $participantDTO): void
    {
        app()->make(EventRegistry::class)->registerBroadcast(
            new Added($participantDTO, $this->_entity()->identity(), $this->_entity()->user()->identity())
        );

        $this->stateChanged();
    }

    /**
     * @inheritDoc
     */
    public function participantRemoved(ParticipantDTO $participantDTO): void
    {
        app()->make(EventRegistry::class)->registerBroadcast(
            new Removed($participantDTO, $this->_entity()->identity(), $this->_entity()->user()->identity())
        );

        $this->stateChanged();
    }

    /**
     * @inheritDoc
     */
    public function isWrapped(): bool
    {
        return $this->_entity()->isWrapped();
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return Mutator::TYPE;
    }

    /**
     * @inheritDoc
     */
    public function types(): array
    {
        return [
            $this->type(),
            PoiMutator::TYPE,
        ];
    }

    /**
     * @inheritDoc
     */
    public function payload(): SharePayload
    {
        return new SharePayload(
            route(
                SessionDTO::ROUTE_NAME_SHOW,
                [
                    $this->_entity()->identity()->toString(),
                ],
                false
            ), ['sessionId' => $this->_entity()->identity()->toString()]
        );
    }

    /**
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    private function exportToCRM(): void
    {
        $this->userService__()->workWith($this->entity->user()->identity())->crmService()->workWithDriver(
            CRMReadonlyContract::DRIVER_THERAPYLOG
        )->export($this->_entity());
    }

    /**
     * @param array $filters
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws Exception
     */
    private function handleFilters(array $filters): void
    {
        if (Arr::has($filters, 'ids')) {
            $needleScopes = Arr::get($filters, 'ids.collection', []);
            $isContains = filter_var(Arr::get($filters, 'ids.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'scheduled_on')) {
            if (Arr::has($filters, 'scheduled_on.scheduled')) {
                $this->_repository()->filterByScheduledOn(
                    filter_var(Arr::get($filters, 'scheduled_on.scheduled', true), FILTER_VALIDATE_BOOLEAN)
                );
            }

            if (Arr::has($filters, 'scheduled_on.range')) {
                $gte = null;

                if (Arr::has($filters, 'scheduled_on.range.gte')) {
                    $gte = new DateTime(Arr::get($filters, 'scheduled_on.range.gte'));
                }

                $lte = null;

                if (Arr::has($filters, 'scheduled_on.range.lte')) {
                    $lte = new DateTime(Arr::get($filters, 'scheduled_on.range.lte'));
                }

                if ($gte instanceof DateTime || $lte instanceof DateTime) {
                    $this->_repository()->filterByScheduledOnRange($gte, $lte);
                }
            }
        }

        if (Arr::has($filters, 'scheduled_to')) {
            if (Arr::has($filters, 'scheduled_to.scheduled')) {
                $this->_repository()->filterByScheduledOn(
                    filter_var(Arr::get($filters, 'scheduled_to.scheduled', true), FILTER_VALIDATE_BOOLEAN)
                );
            }

            if (Arr::has($filters, 'scheduled_to.range')) {
                $gte = null;

                if (Arr::has($filters, 'scheduled_to.range.gte')) {
                    $gte = new DateTime(Arr::get($filters, 'scheduled_to.range.gte'));
                }

                $lte = null;

                if (Arr::has($filters, 'scheduled_to.range.lte')) {
                    $lte = new DateTime(Arr::get($filters, 'scheduled_to.range.lte'));
                }

                if ($gte instanceof DateTime || $lte instanceof DateTime) {
                    $this->_repository()->filterByScheduledToRange($gte, $lte);
                }
            }
        }

        if (Arr::has($filters, 'users')) {
            $needleScopes = Arr::get($filters, 'users.collection', []);
            $isContains = filter_var(Arr::get($filters, 'users.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByUsersIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'participants')) {
            $needleScopes = Arr::get($filters, 'participants.collection', []);
            $isContains = filter_var(Arr::get($filters, 'participants.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByParticipantsIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'poi')) {
            $needleScopes = Arr::get($filters, 'poi.collection', []);
            $isContains = filter_var(Arr::get($filters, 'poi.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByPoisIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'types')) {
            $needleScopes = Arr::get($filters, 'types.collection', []);
            $isContains = filter_var(Arr::get($filters, 'types.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByTypes($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'statuses')) {
            $needleScopes = Arr::get($filters, 'statuses.collection', []);
            $isContains = filter_var(Arr::get($filters, 'statuses.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByStatuses($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'referencable')) {
            $this->_repository()->filterByReferencable(filter_var(Arr::get($filters, 'referencable', true), FILTER_VALIDATE_BOOLEAN));
        }

        if (Arr::has($filters, 'limit')) {
            $this->_repository()->setMaxResults((int)Arr::get($filters, 'limit'));
        }

        if (Arr::has($filters, 'offset')) {
            $this->_repository()->setOffset((int)Arr::get($filters, 'offset'));
        }
    }

    /**
     * @param UserReadonlyContract $user
     * @param array                $data
     *
     * @return SessionContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function make(UserReadonlyContract $user, array $data): SessionContract
    {
        $scheduledOn = null;
        $scheduledTo = null;

        if (Arr::has($data, 'scheduled_on')) {
            $scheduledOn = toUTC(new DateTime(Arr::get($data, 'scheduled_on')));

            if (!Arr::has($data, 'scheduled_to')) {
                throw new InvalidArgumentException('scheduled_to is missed');
            }

            $scheduledTo = toUTC(new DateTime(Arr::get($data, 'scheduled_to')));

            if ($scheduledOn >= $scheduledTo) {
                throw new InvalidArgumentException('Scheduled on should less than scheduledTo');
            }
        }

        return app()->make(
            SessionContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'name' => Arr::get($data, 'name', ''),
                'type' => Arr::get($data, 'type', SessionReadonlyContract::TYPE_DEFAULT),
                'description' => (string)Arr::get($data, 'description', ''),
                'reference' => Arr::get($data, 'reference'),
                'tags' => $this->makeTags(Arr::get($data, 'tags', [])),
                'scheduledOn' => $scheduledOn,
                'scheduledTo' => $scheduledTo,
            ]
        );
    }

    /**
     * @param array $tags
     *
     * @return Tags
     */
    private function makeTags(array $tags): Tags
    {
        return new Tags($tags);
    }

    /**
     * @return bool
     * @throws PropertyNotInit|RuntimeException
     */
    private function isWrapAllowed(): bool
    {
        if (!$this->_entity()->isEnded()) {
            throw new RuntimeException('Session is not ended. Please end it before wrap.');
        }

        try {
            $this->_entity()->streamByType(StreamReadonlyContract::COMBINED_TYPE);
        } catch (NotFoundException $exception) {
            throw new RuntimeException('Cannot be wrapped because recording is not uploaded.');
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function asIdentity(): Identity
    {
        return $this->_entity()->identity();
    }

    /**
     * @inheritDoc
     */
    public function asType(): Type
    {
        return new Type(Mutator::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function asDocument(Index $index): Document
    {
        switch ($index->index()) {
            case Index::INDEX_ENTITIES:
                $data = collect(
                    [
                        'user_id' => $this->_entity()->user()->identity()->toString(),
                        'name' => $this->_entity()->name(),
                        'status' => $this->_entity()->status(),
                        'description' => $this->_entity()->description(),
                        'tags' => $this->_entity()->tags()->tags()->map(function(Tag $tag) {
                            return $tag->tag();
                        })->values()->toArray(),
                        'participant_names' => $this->_entity()->participants()->map(
                            fn(ParticipantReadonlyContract $participant) => $participant->displayName()
                        )->values(),
                    ]
                );
            break;
            case Index::INDEX_FILTERS:
                $data = collect(
                    [
                        'user_id' => $this->_entity()->user()->identity()->toString(),
                        'status' => $this->_entity()->status(),
                        app()->make(ParticipantServiceContract::class)->asType()->type() => $this->_entity()
                            ->participants()
                            ->map(fn(ParticipantReadonlyContract $participant) => $participant->identity()->toString())
                            ->values(),
                        'created_at' => $this->_entity()->createdAt(),
                    ]
                );

                if ($this->_entity()->startedAt() instanceof DateTime) {
                    $data->put('started_at', $this->_entity()->startedAt());
                }

                if ($this->_entity()->endedAt() instanceof DateTime) {
                    $data->put('ended_at', $this->_entity()->endedAt());
                }
            break;
            default:
                throw new IndexNotSupported($index);
        }

        return new Document(
            $index, $this->asType(), $this->_entity()->identity(), new Body($data->toArray(), $this->asMappings($index))
        );
    }

    /**
     * @inheritDoc
     */
    public function asMappings(Index $index): Mappings
    {
        switch ($index->index()) {
            case Index::INDEX_ENTITIES:
                return new Mappings(
                    collect(
                        [
                            new Mapping('user_id', Mapping::TYPE_STRING),
                            new Mapping('name', Mapping::TYPE_STRING),
                            new Mapping('status', Mapping::TYPE_STRING),
                            new Mapping('description', Mapping::TYPE_STRING),
                            new Mapping('tags', Mapping::TYPE_ARRAY),
                            new Mapping('participant_names', Mapping::TYPE_ARRAY),
                        ]
                    )
                );
            case Index::INDEX_FILTERS:
                return new Mappings(
                    collect(
                        [
                            new Mapping('user_id', Mapping::TYPE_STRING),
                            new Mapping('status', Mapping::TYPE_STRING),
                            new Mapping(
                                app()->make(ParticipantServiceContract::class)->asType()->type(),
                                Mapping::TYPE_ARRAY
                            ),
                            new Mapping('created_at', Mapping::TYPE_DATE),
                            new Mapping('ended_at', Mapping::TYPE_DATE),
                            new Mapping('started_at', Mapping::TYPE_DATE),
                        ]
                    )
                );
            default:
                throw new IndexNotSupported($index);
        }
    }

    /**
     * @inheritDoc
     */
    public function verifyResults(Collection $results, ?Collection $filters = null): Collection
    {
        $filter = [
            'ids' => [
                'collection' => $results->toArray(),
            ],
        ];

        if ($filters instanceof Collection && $filters->has('limit')) {
            $filter = array_merge($filter, ['limit' => $filters->get('limit')]);
        }

        $this->applyFilters($filter);

        return $this->list();
    }
}
