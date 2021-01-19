<?php

namespace App\Components\Sessions\Services\Transcription;

use App\Components\Sessions\Session\Transcription\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Transcription\Repository\TranscriptionRepositoryContract;
use App\Components\Sessions\Session\Transcription\TranscriptionContract;
use App\Components\Sessions\Session\Transcription\TranscriptionDTO;
use App\Components\Sessions\Session\Transcription\TranscriptionReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class TranscriptionService
 *
 * @package App\Components\Sessions\Services\Transcription
 */
class TranscriptionService implements TranscriptionServiceContract
{
    use FilterableTrait;
    use AuthServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var TranscriptionContract | null
     */
    private ?TranscriptionContract $entity = null;

    /**
     * @var TranscriptionRepositoryContract | null
     */
    private ?TranscriptionRepositoryContract $repository = null;

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
     * @return TranscriptionRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): TranscriptionRepositoryContract
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
        if (!$this->repository instanceof TranscriptionRepositoryContract) {
            $this->repository = app()->make(TranscriptionRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): TranscriptionServiceContract
    {
        $this->applyFilters([]);

        $this->guardRepository();

        $this->setEntity($this->_repository()->byIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @return TranscriptionContract
     * @throws PropertyNotInit
     */
    private function _entity(): TranscriptionContract
    {
        if (!$this->entity instanceof TranscriptionContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param TranscriptionContract $entity
     *
     * @return TranscriptionServiceContract
     */
    private function setEntity(TranscriptionContract $entity): TranscriptionServiceContract
    {
        $this->entity = $entity;

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
    public function readonly(): TranscriptionReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): TranscriptionDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->sortByDesc(
            function (TranscriptionReadonlyContract $entity) {
                return $entity->createdAt();
            }
        )->map(
            function (TranscriptionContract $entity) {
                return $this->_mutator()->toDTO($entity);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->handleFilters($this->filters());

        $this->guardRepository();

        $results = $this->_repository()->getAll();

        $this->applyFilters([]);

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): TranscriptionServiceContract
    {
        $entity = $this->make($data);
        $this->setEntity($entity);
        $this->_repository()->persist($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): TranscriptionServiceContract
    {
        // Todo remove record from elastic
        $entity = $this->_entity();

        $this->_repository()->destroy($entity);

        return $this;
    }

    /**
     * @throws BindingResolutionException
     */
    private function guardRepository(): void
    {
        if (!app()->runningInConsole() && $this->authService__()->check()) {
            // todo verification
        }
    }

    /**
     * @param array $filters
     *
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    private function handleFilters(array $filters): void
    {
        if (Arr::has($filters, 'users')) {
            $needleScopes = Arr::get($filters, 'users.collection', []);
            $isContains = filter_var(Arr::get($filters, 'users.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByUsersIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'sessions')) {
            $needleScopes = Arr::get($filters, 'sessions.collection', []);
            $isContains = filter_var(Arr::get($filters, 'sessions.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterBySessionIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'pois')) {
            $needleScopes = Arr::get($filters, 'pois.collection', []);
            $isContains = filter_var(Arr::get($filters, 'pois.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByPoisIds($needleScopes, $isContains);
        }
    }

    /**
     * @param array $data
     *
     * @return TranscriptionContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    private function make(array $data): TranscriptionContract
    {
        if (!IdentityGenerator::isValid((string)Arr::get($data, 'session_id', ''))) {
            throw new InvalidArgumentException('Session identity is not valid');
        }

        $sessionIdentity = new Identity((string)Arr::get($data, 'session_id'));

        if (!IdentityGenerator::isValid((string)Arr::get($data, 'poi_id', ''))) {
            $poiIdentity = null;
        } else {
            $poiIdentity = new Identity((string)Arr::get($data, 'poi_id'));
        }

        if (!IdentityGenerator::isValid((string)Arr::get($data, 'user_id', ''))) {
            throw new InvalidArgumentException('User identity is not valid');
        }

        $userIdentity = new Identity((string)Arr::get($data, 'user_id'));

        return app()->make(
            TranscriptionContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'userIdentity' => $userIdentity,
                'sessionIdentity' => $sessionIdentity,
                'poiIdentity' => $poiIdentity,
                'word' => Arr::get($data, 'word', ''),
                'startedAt' => Arr::get($data, 'started_at', ''),
                'endedAt' => Arr::get($data, 'ended_at', ''),
                'speakerTag' => (int)Arr::get($data, 'speaker_tag', 0),
            ]
        );
    }
}
