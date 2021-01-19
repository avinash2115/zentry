<?php

namespace App\Components\Users\Login\Token\Repository;

use App\Components\Users\Login\Token\TokenContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class TokenRepositoryMemory
 *
 * @package App\Components\Users\Login\Token\Repository
 */
class TokenRepositoryMemory extends AbstractMemoryRepository implements TokenRepositoryContract
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
    public function byIdentity(Identity $identity): TokenContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof TokenContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?TokenContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(TokenContract $entity): TokenContract
    {
        $this->register($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function register(TokenContract $entity): TokenContract
    {
        $this->collector->put($entity->identity()->toString(), $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(TokenContract $entity): bool
    {
        if ($this->collector->has($entity->identity()->toString())) {
            $this->collector->forget($entity->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): TokenRepositoryContract
    {
        return $this;
    }
}
