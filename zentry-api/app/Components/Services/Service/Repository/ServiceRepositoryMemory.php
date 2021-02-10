<?php

namespace App\Components\Services\Service\Repository;

use App\Components\Services\Service\ServiceContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class ServiceRepositoryMemory
 *
 * @package App\Components\Services\Service\Repository
 */
class ServiceRepositoryMemory extends AbstractMemoryRepository implements ServiceRepositoryContract
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
    public function byIdentity(Identity $identity): ServiceContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof ServiceContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?ServiceContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(ServiceContract $entity): ServiceContract
    {
        $this->register($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function register(ServiceContract $entity): ServiceContract
    {
        $this->collector->put($entity->identity()->toString(), $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(ServiceContract $entity): bool
    {
        if ($this->collector->has($entity->identity()->toString())) {
            $this->collector->forget($entity->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $values, bool $contains = true): ServiceRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): ServiceRepositoryContract
    {
        return $this;
    }
}
