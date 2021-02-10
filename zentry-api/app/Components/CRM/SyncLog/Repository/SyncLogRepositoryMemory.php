<?php

namespace App\Components\CRM\SyncLog\Repository;

use App\Components\CRM\SyncLog\SyncLogContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class SyncLogRepositoryMemory
 *
 * @package App\Components\CRM\SyncLog\Repository
 */
class SyncLogRepositoryMemory extends AbstractMemoryRepository implements SyncLogRepositoryContract
{
    /**
     */
    public function __construct()
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS);
    }

    /**
     * @inheritDoc
     */
    public function byIdentity(Identity $identity): SyncLogContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof SyncLogContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?SyncLogContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(SyncLogContract $entity): SyncLogContract
    {
        $this->register($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function register(SyncLogContract $entity): SyncLogContract
    {
        $this->collector->put($entity->identity()->toString(), $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(SyncLogContract $entity): bool
    {
        if ($this->collector->has($entity->identity()->toString())) {
            $this->collector->forget($entity->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByCRM(string $id, bool $contains = true): SyncLogRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByTypes(array $ids, bool $contains = true): SyncLogRepositoryContract
    {
        return $this;
    }
}