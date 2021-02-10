<?php

namespace App\Components\Users\Participant\Repository;

use App\Components\Users\Participant\ParticipantContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class ParticipantRepositoryMemory
 *
 * @package App\Components\Users\Participant\Repository
 */
class ParticipantRepositoryMemory extends AbstractMemoryRepository implements ParticipantRepositoryContract
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
    public function byIdentity(Identity $identity): ParticipantContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof ParticipantContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?ParticipantContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(ParticipantContract $entity): ParticipantContract
    {
        $this->register($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function register(ParticipantContract $entity): ParticipantContract
    {
        $this->collector->put($entity->identity()->toString(), $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(ParticipantContract $entity): bool
    {
        if ($this->collector->has($entity->identity()->toString())) {
            $this->collector->forget($entity->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUserIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmails(array $emails, bool $contains = true): ParticipantRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByTeamIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySchoolIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByGoalsIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isExists(string $email): bool
    {
        return $this->collector()->first(
                function (ParticipantContract $user) use ($email) {
                    return $user->email() === $email;
                }
            ) instanceof ParticipantContract;
    }
}
