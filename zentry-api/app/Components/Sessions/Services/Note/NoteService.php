<?php

namespace App\Components\Sessions\Services\Note;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\HasFilesTrait;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Session\Note\NoteContract;
use App\Components\Sessions\Session\Note\NoteDTO;
use App\Components\Sessions\Session\Note\NoteReadonlyContract;
use App\Components\Sessions\Session\Note\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\ValueObjects\Note\Payload;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Class NoteService
 *
 * @package App\Components\Sessions\Services\Note
 */
class NoteService implements NoteServiceContract
{
    use FilterableTrait;
    use LinkParametersTrait;
    use FileServiceTrait;
    use HasFilesTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var NoteContract | null
     */
    private ?NoteContract $entity = null;

    /**
     * @var SessionServiceContract
     */
    private SessionServiceContract $sessionService;

    /**
     * @var SessionContract
     */
    private SessionContract $session;

    /**
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
     * @return NoteContract
     * @throws PropertyNotInit
     */
    private function _entity(): NoteContract
    {
        if (!$this->entity instanceof NoteContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param NoteContract $entity
     *
     * @return NoteServiceContract
     */
    private function setEntity(NoteContract $entity): NoteServiceContract
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): NoteServiceContract
    {
        $this->setEntity($this->_session()->noteByIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): NoteReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): NoteDTO
    {
        $this->fillLinkParameters();

        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        $this->fillLinkParameters();

        return $this->listRO()->map(
            function (NoteReadonlyContract $note) {
                return $this->_mutator()->toDTO($note);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_session()->notes();
    }

    /**
     * @inheritDoc
     */
    public function create(Payload $payload, UploadedFile $file = null): NoteServiceContract
    {
        if ($file instanceof UploadedFile) {
            $payload->changeUrl($this->fileService__()->put($file, $this)->url());
        }

        $this->setEntity($this->make($payload));
        $this->_session()->addNote($this->_entity());

        return $this;
    }

    /**
     * @param Payload $payload
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function make(Payload $payload)
    {
        return app()->make(
            NoteContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'session' => $this->_session(),
                'payload' => $payload,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): NoteServiceContract
    {
        if (Arr::has($data, 'text')) {
            $this->_entity()->changeText((string)Arr::get($data, 'text'));
        }

        if (Arr::has($data, 'url') || Arr::has($data, 'file')) {
            $url = Arr::get($data, 'url');

            if (is_null($url)) {
                $this->removeNoteUrl();
                $this->_entity()->changeUrl();
            }
        }

        if (Arr::has($data, 'file')) {
            $file = Arr::get($data, 'file');

            if (!$file instanceof UploadedFile) {
                throw new RuntimeException('File is not uploaded');
            }

            $this->removeNoteUrl();
            $this->_entity()->changeUrl($this->fileService__()->put($file, $this)->url());
        }

        return $this;
    }

    /**
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws DeleteException
     */
    private function removeNoteUrl(): void
    {
        if ($this->_entity()->url() !== null && $this->fileService__()->isExist($this->_entity()->url())) {
            $this->fileService__()->remove($this->_entity()->url());
        }
    }

    /**
     * @inheritDoc
     */
    public function remove(): NoteServiceContract
    {
        $this->removeNoteUrl();

        $this->_session()->removeNote($this->_entity());

        return $this;
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
        return array_merge(
            $this->_sessionService()->fileNamespaceParts($humanReadable),
            [
                'notes',
            ]
        );
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

    /**
     * @inheritDoc
     */
    public function identity(): Identity
    {
        return $this->_entity()->identity();
    }
}
