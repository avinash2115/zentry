<?php

namespace App\Components\Sessions\Services\Poi;

use App\Assistants\Events\EventRegistry;
use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\HasFilesTrait;
use App\Assistants\Files\ValueObjects\TemporaryUrl;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Services\Poi\Indexable\IndexableServiceContract;
use App\Components\Sessions\Services\Poi\Participant\ParticipantServiceContract;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Services\Transcription\Contracts\InjectedDTO;
use App\Components\Sessions\Services\Transcription\Traits\TranscriptionServiceTrait;
use App\Components\Sessions\Session\Poi\Events\Broadcast\Changed;
use App\Components\Sessions\Session\Poi\Events\Broadcast\Created;
use App\Components\Sessions\Session\Poi\Events\Broadcast\Removed;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Poi\PoiContract;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\Transcription\TranscriptionReadonlyContract;
use App\Components\Sessions\ValueObjects\Transcription\Transcript;
use App\Components\Sessions\ValueObjects\Transcription\Word;
use App\Components\Share\Services\Shared\Traits\SharedServiceTrait;
use App\Components\Share\ValueObjects\Payload as SharePayload;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Tags;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use UnexpectedValueException;

/**
 * Class PoiService
 *
 * @package App\Components\Sessions\Services\Poi
 */
class PoiService implements PoiServiceContract
{
    use HasFilesTrait;
    use AuthServiceTrait;
    use FileServiceTrait;
    use SharedServiceTrait;
    use TranscriptionServiceTrait;
    use LinkParametersTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var PoiContract | null
     */
    private ?PoiContract $entity = null;

    /**
     * @var SessionContract|null
     */
    private ?SessionContract $session = null;

    /**
     * @var SessionServiceContract|null
     */
    private ?SessionServiceContract $sessionService = null;

    /**
     * @var ParticipantServiceContract | null
     */
    private ?ParticipantServiceContract $participantService = null;

    /**
     * @var IndexableServiceContract|null
     */
    private ?IndexableServiceContract $indexableService = null;

    /**
     * PoiService constructor.
     *
     * @param SessionServiceContract $sessionService
     * @param SessionContract        $session
     */
    public function __construct(SessionServiceContract $sessionService, SessionContract $session)
    {
        $this->sessionService = $sessionService;
        $this->session = $session;
    }

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
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setParticipantService(): void
    {
        $this->participantService = app()->make(
            ParticipantServiceContract::class,
            [
                'poi' => $this->_entity(),
                'session' => $this->_session(),
            ]
        );
    }

    /**
     * @return SessionServiceContract
     * @throws PropertyNotInit
     */
    private function _sessionService(): SessionServiceContract
    {
        if (!$this->sessionService instanceof SessionServiceContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->sessionService;
    }

    /**
     * @inheritDoc
     */
    public function participantService(): ParticipantServiceContract
    {
        if (!$this->participantService instanceof ParticipantServiceContract) {
            $this->setParticipantService();
        }

        return $this->participantService;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): PoiServiceContract
    {
        $this->setEntity($this->_session()->poiByIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @return PoiContract
     * @throws PropertyNotInit
     */
    private function _entity(): PoiContract
    {
        if (!$this->entity instanceof PoiContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param PoiContract $entity
     *
     * @return PoiServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setEntity(PoiContract $entity): PoiServiceContract
    {
        $this->entity = $entity;

        $this->setParticipantService();
        $this->setIndexableService();

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
    public function readonly(): PoiReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): PoiDTO
    {
        $this->fillLinkParameters();

        $dto = $this->_mutator()->toDTO($this->_entity());

        try {
            $this->sharedService__()->workWithSharable($this);
            $dto->isShared = true;
        } catch (NotFoundException $exception) {
        }

        return $dto;
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        $this->fillLinkParameters();

        return $this->listRO()->map(
            function (PoiReadonlyContract $poi) {
                return $this->_mutator()->toDTO($poi);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_session()->pois();
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): PoiServiceContract
    {
        $fireEvent = false;

        if (Arr::has($data, 'name')) {
            $this->_entity()->changeName(Arr::get($data, 'name'));
            $fireEvent = true;
        }

        if (Arr::has($data, 'tags')) {
            $this->_entity()->changeTags($this->makeTags(Arr::get($data, 'tags', [])));
        }

        if (Arr::has($data, 'thumbnail')) {
            $this->_entity()->changeThumbnail(Arr::get($data, 'thumbnail', ''));
        }

        if (Arr::has($data, 'stream')) {
            $this->_entity()->changeStream(Arr::get($data, 'stream', ''));
        }

        app()->make(EventRegistry::class)->registerBroadcast(
            new Changed($this->dto(), $this->_session(), $this->_session()->user()->identity())
        );

        if ($fireEvent) {
            $this->indexableService()->stateChanged();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): PoiServiceContract
    {
        $entity = $this->make($data);
        $this->_session()->addPoi($entity);

        $this->setEntity($entity);

        app()->make(EventRegistry::class)->registerBroadcast(
            new Created($this->dto(), $this->_session(), $this->_session()->user()->identity())
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): PoiServiceContract
    {
        if ($this->_entity()->thumbnail() !== null && $this->fileService__()->isExist($this->_entity()->thumbnail())) {
            $this->fileService__()->remove($this->_entity()->thumbnail());
        }

        $this->_session()->removePoi($this->_entity());

        app()->make(EventRegistry::class)->registerBroadcast(
            new Removed($this->dto(), $this->_session(), $this->_session()->user()->identity())
        );

        $this->indexableService()->stateDeletion();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function indexableService(): IndexableServiceContract
    {
        if (!$this->indexableService instanceof IndexableServiceContract) {
            $this->setIndexableService();
        }

        return $this->indexableService;
    }

    /**
     * @return PoiServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setIndexableService(): PoiServiceContract
    {
        $this->indexableService = app()->make(IndexableServiceContract::class, [
            'session' => $this->_session(),
            'poiService' => $this,
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fileNamespaceParts(bool $humanReadable = false): array
    {
        return array_merge(
            $this->_sessionService()->fileNamespaceParts($humanReadable),
            [
                'clips',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function isWrapped(): bool
    {
        return $this->_session()->isWrapped();
    }

    /**
     * @inheritDoc
     */
    public function temporaryUrl(): TemporaryUrl
    {
        return $this->fileService__()->temporaryUrl(
            $this->_entity()->stream(),
            $this->_entity()->identity()->toString()
        );
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function payload(): SharePayload
    {
        return new SharePayload(
            route(
                PoiDTO::ROUTE_NAME_SHOW,
                [$this->_session()->identity()->toString(), $this->_entity()->identity()->toString()],
                false
            ), [
                'sessionId' => $this->_session()->identity()->toString(),
                'poiId' => $this->_entity()->identity()->toString(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function injectedDTO(): InjectedDTO
    {
        $dto = $this->dto();

        $dto->transcript = $this->transcript();

        return $dto;
    }

    /**
     * @inheritDoc
     */
    public function injectedList(): Collection
    {
        $words = $this->words();

        return $this->list()->map(
            function (PoiDTO $dto) use ($words) {
                $wordsFiltered = $words->get($dto->id(), collect());

                if ($wordsFiltered->isNotEmpty()) {
                    $dto->transcript = new Transcript($wordsFiltered);
                }

                return $dto;
            }
        )->filter(fn(PoiDTO $dto) => $dto->transcript instanceof Transcript);
    }

    /**
     * @inheritDoc
     */
    public function transcript(): ?Transcript
    {
        $words = $this->words()->get($this->_entity()->identity()->toString(), collect());

        if ($words->isNotEmpty()) {
            return new Transcript($words);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function words(): Collection
    {
      return $this->_words()->filter(fn(Collection $entities, string $poiIdentity) => !strEmpty($poiIdentity))->map(
            fn(Collection $entities, string $poiIdentity) => $entities->map(
                fn(TranscriptionReadonlyContract $entity) => new Word($entity)
            )->values()
        );
    }

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    private function _words(): Collection
    {
        try {
            $this->transcriptionService__()->applyFilters(
                [
                    'pois' => [
                        'collection' => [
                            $this->_entity()->identity()->toString(),
                        ],
                        'has' => true,
                    ],
                ]
            );
        } catch (PropertyNotInit $exception) {
            $this->transcriptionService__()->applyFilters(
                [
                    'pois' => [
                        'collection' => $this->listRO()->keys()->toArray(),
                        'has' => true,
                    ],
                ]
            );
        }

        return $this->transcriptionService__()->listRO()->groupBy(
            static function (TranscriptionReadonlyContract $entity) {
                return $entity->poiIdentity() instanceof Identity ? $entity->poiIdentity()->toString() : '';
            }
        );

    }

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function wordsSimplified(): Collection
    {
        return $this->_words()->filter(fn(Collection $entities, string $poiIdentity) => !strEmpty($poiIdentity))->map(
            fn(Collection $entities, string $poiIdentity) => $entities->map(
                fn(TranscriptionReadonlyContract $entity) => $entity->word()
            )->values()
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
     * @param array $data
     *
     * @return PoiContract
     * @throws BindingResolutionException|PropertyNotInit|Exception
     */
    private function make(array $data): PoiContract
    {
        return app()->make(
            PoiContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'session' => $this->_session(),
                'type' => Arr::get($data, 'type', ''),
                'name' => Arr::get($data, 'name'),
                'user' => $this->authService__()->user()->readonly(),
                'tags' => $this->makeTags(Arr::get($data, 'tags', [])),
                'startedAt' => new DateTime(Arr::get($data, 'started_at')),
                'endedAt' => new DateTime(Arr::get($data, 'ended_at')),
            ]
        );
    }

    /**
     * @return SessionContract
     * @throws PropertyNotInit
     */
    private function _session(): SessionContract
    {
        if (!$this->session instanceof SessionContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->session;
    }

    /**
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function fillLinkParameters(): void
    {
        $this->linkParameters__()->put(
            collect(
                [
                    'sessionId' => $this->_session()->identity()->toString(),
                ]
            )
        );
    }
}
