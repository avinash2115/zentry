<?php

namespace App\Components\Providers\ProviderService\Repository;

use App\Components\Providers\ProviderService\ProviderContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class ProviderRepositoryMemory
 *
 * @package App\Components\Providers\ProviderService\Repository
 */
class ProviderRepositoryMemory extends AbstractMemoryRepository implements ProviderRepositoryContract
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
    public function byIdentity(Identity $identity): ProviderContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof ProviderContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?ProviderContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(ProviderContract $entity): ProviderContract
    {
        $this->register($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function register(ProviderContract $entity): ProviderContract
    {
        $this->collector->put($entity->identity()->toString(), $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(ProviderContract $entity): bool
    {
        if ($this->collector->has($entity->identity()->toString())) {
            $this->collector->forget($entity->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $values, bool $contains = true): ProviderRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): ProviderRepositoryContract
    {
        return $this;
    }
}
