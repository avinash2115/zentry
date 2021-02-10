<?php

namespace App\Components\CRM\Source\Repository;

use App\Components\CRM\Source\SourceContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class SourceRepositoryMemory
 *
 * @package App\Components\CRM\Source\Repository
 */
class SourceRepositoryMemory extends AbstractMemoryRepository implements SourceRepositoryContract
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
    public function byIdentity(Identity $identity): SourceContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof SourceContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?SourceContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(SourceContract $entity): SourceContract
    {
        $this->register($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function register(SourceContract $entity): SourceContract
    {
        $this->collector->put($entity->identity()->toString(), $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(SourceContract $entity): bool
    {
        if ($this->collector->has($entity->identity()->toString())) {
            $this->collector->forget($entity->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByCRM(string $id, bool $contains = true): SourceRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByClass(string $className, bool $contains = true): SourceRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByDirections(array $ids, bool $contains = true): SourceRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByOwnerIds(array $ids, bool $contains = true): SourceRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySourceIds(array $ids, bool $contains = true): SourceRepositoryContract
    {
        return $this;
    }
}