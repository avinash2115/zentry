<?php

namespace App\Components\Sessions\Services\Stream;

use App\Assistants\Events\EventRegistry;
use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\HasFilesTrait;
use App\Assistants\Files\Services\Traits\LocalFileServiceTrait;
use App\Assistants\Files\ValueObjects\File;
use App\Assistants\Files\ValueObjects\TemporaryUrl;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\Stream\Events\Broadcast\Convert\Progress;
use App\Components\Sessions\Session\Stream\Events\Broadcast\Created;
use App\Components\Sessions\Session\Stream\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Stream\StreamContract;
use App\Components\Sessions\Session\Stream\StreamDTO;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use const PATHINFO_EXTENSION;

/**
 * Class StreamService
 *
 * @package App\Components\Sessions\Services\Stream
 */
class StreamService implements StreamServiceContract
{
    use AuthServiceTrait;
    use HasFilesTrait;
    use LocalFileServiceTrait;
    use FileServiceTrait;
    use LinkParametersTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var StreamContract | null
     */
    private ?StreamContract $entity = null;

    /**
     * @var SessionServiceContract
     */
    private SessionServiceContract $sessionService;

    /**
     * @var SessionContract
     */
    private SessionContract $session;

    /**
     * @var array
     */
    private array $fileNamespacePartsModifiers = [];

    /**
     * StreamService constructor.
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
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function _mutator(): Mutator
    {
        $this->setMutator();

        return $this->mutator;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): StreamServiceContract
    {
        $this->setEntity($this->_session()->streamByIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWithType(string $type): StreamServiceContract
    {
        $this->setEntity($this->_session()->streamByType($type));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        $this->fillLinkParameters();

        return $this->listRO()->map(
            function (StreamReadonlyContract $poi) {
                return $this->_mutator()->toDTO($poi);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_session()->streams();
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
    public function readonly(): StreamReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): StreamDTO
    {
        $this->fillLinkParameters();

        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @param StreamContract $entity
     *
     * @return StreamServiceContract
     */
    private function setEntity(StreamContract $entity): StreamServiceContract
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return StreamContract
     * @throws PropertyNotInit
     */
    private function _entity(): StreamContract
    {
        if (!$this->entity instanceof StreamContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @inheritDoc
     */
    public function create(UploadedFile $uploadedFile, string $type): StreamServiceContract
    {
        $entity = $this->make($this->localFileService__()->put($uploadedFile, $this), $type);

        $this->_session()->addStream($entity);

        $this->setEntity($entity);

        app()->make(EventRegistry::class)->registerBroadcast(
            new Created($this->dto(), $this->_session())
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): StreamServiceContract
    {
        if (Arr::has($data, 'name')) {
            $this->_entity()->changeName(Arr::get($data, 'name', ''));
        }

        if (Arr::has($data, 'url')) {
            $this->_entity()->changeUrl(Arr::get($data, 'url', ''));
        }

        if (Arr::has($data, 'progress') && Arr::get($data, 'progress') !== $this->_entity()->convertProgress()) {
            $this->_entity()->convertProgressAdvance((int)Arr::get($data, 'progress', 0));

            app()->make(EventRegistry::class)->registerBroadcast(
                new Progress($this->dto(), $this->_session(), $this->_session()->user()->identity())
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): StreamServiceContract
    {
        if ($this->fileService__()->isExist($this->_entity()->url())) {
            $this->fileService__()->remove($this->_entity()->url());
        }

        $this->_session()->removeStream($this->_entity());

        $this->entity = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function receivePartial(string $type, UploadedFile $uploadedFile): bool
    {
        $this->verifyType($type);

        $this->fileNamespacePartsModifiers = [$type, 'partial'];

        $this->localFileService__()->put($uploadedFile, $this);

        $this->fileNamespacePartsModifiers = [];

        return true;
    }

    /**
     * @inheritDoc
     */
    public function mergePartial(string $type): StreamServiceContract
    {
        $this->verifyType($type);

        if ($this->_session()->isWrapped()) {
            return $this;
        }

        try {
            $this->_session()->streamByType($type);

            return $this;
        } catch (NotFoundException $exception) {
            $this->fileNamespacePartsModifiers = [$type, 'partial'];

            $partials = $this->localFileService__()->list($this->fileNamespace());

            $first = $partials->first();

            if (!is_string($first) || $partials->isEmpty()) {
                throw new RuntimeException('Nothing to merge');
            }

            $this->fileNamespacePartsModifiers = [];

            $entity = $this->make($this->localFileService__()->merge($partials, "{$type}.".pathinfo($first, PATHINFO_EXTENSION), $this), $type);

            $this->_session()->addStream($entity);

            $this->setEntity($entity);

            app()->make(EventRegistry::class)->registerBroadcast(
                new Created($this->dto(), $this->_session())
            );

            return $this;
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    private function verifyType(string $type): bool
    {
        if (!in_array($type, StreamReadonlyContract::AVAILABLE_TYPES, true)) {
            throw new InvalidArgumentException("Wrong type {$type} passed");
        }

        return true;
    }

    /**
     * @param File   $file
     * @param string $type
     *
     * @return StreamContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function make(File $file, string $type): StreamContract
    {
        return app()->make(
            StreamContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'session' => $this->_session(),
                'type' => $type,
                'file' => $file,
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
    public function fileNamespaceParts(bool $humanReadable = false): array
    {
        return array_merge($this->_sessionService()->fileNamespaceParts(), ['streams'], $this->fileNamespacePartsModifiers);
    }

    /**
     * @inheritDoc
     */
    public function temporaryUrl(): TemporaryUrl
    {
        return $this->fileService__()->temporaryUrl($this->_entity()->url(), $this->_entity()->name());
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
