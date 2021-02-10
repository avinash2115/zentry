<?php

namespace App\Components\Share\Shared\Repository;

use App\Components\Share\Shared\SharedContract;
use App\Components\Share\ValueObjects\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class SharedRepositoryMemory
 *
 * @package App\Components\Share\Shared\Repository
 */
class SharedRepositoryMemory extends AbstractMemoryRepository implements SharedRepositoryContract
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
    public function byIdentity(Identity $identity): SharedContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof SharedContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?SharedContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(SharedContract $entity): SharedContract
    {
        $this->register($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function register(SharedContract $passwordReset): SharedContract
    {
        $this->collector->put($passwordReset->identity()->toString(), $passwordReset);

        return $passwordReset;
    }

    /**
     * @inheritDoc
     */
    public function destroy(SharedContract $passwordReset): bool
    {
        if ($this->collector->has($passwordReset->identity()->toString())) {
            $this->collector->forget($passwordReset->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterById(array $ids, bool $contains = true): SharedRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByPayload(Payload $payload): SharedRepositoryContract
    {
        return $this;
    }
}
