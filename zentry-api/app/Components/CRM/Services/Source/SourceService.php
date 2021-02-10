<?php

namespace App\Components\CRM\Services\Source;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\CRM\Source\Mutators\DTO\Mutator;
use App\Components\CRM\Source\Repository\SourceRepositoryContract;
use App\Components\CRM\Source\SourceContract;
use App\Components\CRM\Source\SourceDTO;
use App\Components\CRM\Source\SourceReadonlyContract;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class SourceService
 *
 * @package App\Components\CRM\Services\Source
 */
class SourceService implements SourceServiceContract
{
    use FilterableTrait;
    use TeamServiceTrait;
    use ParticipantServiceTrait;

    /**
     * @var SourceContract|null
     */
    private ?SourceContract $entity = null;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var SourceRepositoryContract | null
     */
    private ?SourceRepositoryContract $repository = null;

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
     * @return SourceRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): SourceRepositoryContract
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
        if (!$this->repository instanceof SourceRepositoryContract) {
            $this->repository = app()->make(SourceRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): SourceServiceContract
    {
        return $this->setEntity($this->_repository()->byIdentity(new Identity($id)));
    }

    /**
     * @return SourceContract
     * @throws PropertyNotInit
     */
    private function _entity(): SourceContract
    {
        if (!$this->entity instanceof SourceContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param SourceContract $entity
     *
     * @return SourceService
     */
    private function setEntity(SourceContract $entity): SourceService
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): SourceReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function create(CRMReadonlyContract $crm, CRMImportableContract $owner, array $data): SourceServiceContract
    {
        $this->setEntity($this->make($crm, $owner, $data));
        $this->_repository()->persist($this->_entity());

        return $this;
    }

    /**
     * @param CRMReadonlyContract   $crm
     * @param CRMImportableContract $owner
     * @param array                 $data
     *
     * @return SourceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function make(CRMReadonlyContract $crm, CRMImportableContract $owner, array $data): SourceContract
    {
        $sourceId = Arr::get($data, 'source_id');

        $direction = Arr::get($data, 'direction', SourceReadonlyContract::DIRECTION_IN);

        if (strEmpty($sourceId)) {
            throw new InvalidArgumentException("Source ID cant be empty");
        }

        return app()->make(
            $owner->sourceEntityClass(),
            [
                'identity' => IdentityGenerator::next(),
                'crm' => $crm,
                'owner' => $owner,
                'sourceId' => $sourceId,
                'direction' => $direction,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function dto(): SourceDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (SourceReadonlyContract $request) {
                return $this->_mutator()->toDTO($request);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->handleFilters($this->filters());

        $results = $this->_repository()->getAll();

        $this->applyFilters([]);

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function remove(): SourceServiceContract
    {
        $this->_repository()->destroy($this->_entity());

        return $this;
    }

    /**
     * @param array $filters
     *
     * @throws BindingResolutionException
     */
    private function handleFilters(array $filters): void
    {
        if (Arr::has($filters, 'crm')) {
            $needleScopes = Arr::get($filters, 'crm.id');
            $isContains = filter_var(Arr::get($filters, 'type.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByCRM($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'type')) {
            $needleScopes = Arr::get($filters, 'type.className');
            $isContains = filter_var(Arr::get($filters, 'type.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByClass($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'direction')) {
            $needleScopes = Arr::get($filters, 'direction.collection', []);
            $isContains = filter_var(Arr::get($filters, 'direction.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByDirections($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'owners')) {
            $needleScopes = Arr::get($filters, 'owners.collection', []);
            $isContains = filter_var(Arr::get($filters, 'owners.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByOwnerIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'sources')) {
            $needleScopes = Arr::get($filters, 'sources.collection', []);
            $isContains = filter_var(Arr::get($filters, 'sources.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterBySourceIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'limit')) {
            $this->_repository()->setMaxResults((int)Arr::get($filters, 'limit'));
        }
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): SourceServiceContract
    {
        if (Arr::has($data, 'owner')) {
            $this->_entity()->setOwner(Arr::get($data, 'owner'));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(string $type): string
    {
        $alias = CRMImportableContract::CRM_ALIAS_PREFIX . $type;
        $entityClassname = Container::getInstance()->getAlias($alias);
        if ($entityClassname === $alias) {
            throw new InvalidArgumentException("Undefined source entity alias: {$alias}");
        }

        return $entityClassname;
    }
}
