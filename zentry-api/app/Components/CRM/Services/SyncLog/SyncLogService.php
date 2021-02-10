<?php

namespace App\Components\CRM\Services\SyncLog;

use App\Components\CRM\SyncLog\Mutators\DTO\Mutator;
use App\Components\CRM\SyncLog\Repository\SyncLogRepositoryContract;
use App\Components\CRM\SyncLog\SyncLogContract;
use App\Components\CRM\SyncLog\SyncLogDTO;
use App\Components\CRM\SyncLog\SyncLogReadonlyContract;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

/**
 * Class SyncLogService
 *
 * @package App\Components\CRM\Services\SyncLog
 */
class SyncLogService implements SyncLogServiceContract
{
    use FilterableTrait;
    use TeamServiceTrait;
    use ParticipantServiceTrait;

    /**
     * @var SyncLogContract|null
     */
    private ?SyncLogContract $entity = null;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var SyncLogRepositoryContract | null
     */
    private ?SyncLogRepositoryContract $repository = null;

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
     * @return SyncLogRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): SyncLogRepositoryContract
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
        if (!$this->repository instanceof SyncLogRepositoryContract) {
            $this->repository = app()->make(SyncLogRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): SyncLogServiceContract
    {
        return $this->setEntity($this->_repository()->byIdentity(new Identity($id)));
    }

    /**
     * @return SyncLogContract
     * @throws PropertyNotInit
     */
    private function _entity(): SyncLogContract
    {
        if (!$this->entity instanceof SyncLogContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param SyncLogContract $entity
     *
     * @return SyncLogService
     */
    private function setEntity(SyncLogContract $entity): SyncLogService
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function readonly(): SyncLogReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function create(CRMReadonlyContract $crm, string $crmEntityType): SyncLogServiceContract
    {
        $this->setEntity($this->make($crm, $crmEntityType));
        $this->_repository()->persist($this->_entity());

        return $this;
    }

    /**
     * @param CRMReadonlyContract $crm
     * @param string              $crmEntityType
     *
     * @return SyncLogContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function make(CRMReadonlyContract $crm, string $crmEntityType): SyncLogContract
    {
        return app()->make(
            SyncLogContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'crm' => $crm,
                'type' => $crmEntityType,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function dto(): SyncLogDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (SyncLogReadonlyContract $request) {
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
    public function remove(): SyncLogServiceContract
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
            $isContains = filter_var(Arr::get($filters, 'crm.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByCRM($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'types')) {
            $needleScopes = Arr::get($filters, 'types.collection', []);
            $isContains = filter_var(Arr::get($filters, 'types.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByTypes($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'limit')) {
            $this->_repository()->setMaxResults((int)Arr::get($filters, 'limit'));
        }

        if (Arr::has($filters, 'order')) {
            $this->_repository()->sortBy(Arr::get($filters, 'order', []));
        }
    }
}
