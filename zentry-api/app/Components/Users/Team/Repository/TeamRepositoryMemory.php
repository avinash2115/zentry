<?php

namespace App\Components\Users\Team\Repository;

use App\Components\Users\Team\TeamContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class TeamRepositoryMemory
 *
 * @package App\Components\Users\Team\Repository
 */
class TeamRepositoryMemory extends AbstractMemoryRepository implements TeamRepositoryContract
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
    public function byIdentity(Identity $identity): TeamContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof TeamContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?TeamContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(TeamContract $entity): TeamContract
    {
        $this->register($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function register(TeamContract $entity): TeamContract
    {
        $this->collector->put($entity->identity()->toString(), $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(TeamContract $entity): bool
    {
        if ($this->collector->has($entity->identity()->toString())) {
            $this->collector->forget($entity->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByMemberIds(array $ids, bool $contains = true): TeamRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByOwnerIds(array $ids, bool $contains = true): TeamRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUserPresence(string $id): TeamRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySchoolIds(array $ids, bool $contains = true): TeamRepositoryContract
    {
        return $this;
    }
}
