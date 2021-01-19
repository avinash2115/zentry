<?php

namespace App\Components\Sessions\Session\Repository;

use App\Components\Sessions\Session\SessionContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class SessionRepositoryMemory
 *
 * @package App\Components\Sessions\Session\Repository
 */
class SessionRepositoryMemory extends AbstractMemoryRepository implements SessionRepositoryContract
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
    public function byIdentity(Identity $identity): SessionContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof SessionContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?SessionContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(SessionContract $session): SessionContract
    {
        $this->register($session);

        return $session;
    }

    /**
     * @inheritDoc
     */
    public function register(SessionContract $session): SessionContract
    {
        $this->collector->put($session->identity()->toString(), $session);

        return $session;
    }

    /**
     * @inheritDoc
     */
    public function destroy(SessionContract $session): bool
    {
        if ($this->collector->has($session->identity()->toString())) {
            $this->collector->forget($session->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $values, bool $contains = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByStarted(bool $isStarted = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByNullableEndedAt(): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEnded(bool $isEnded = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByScheduledOn(bool $scheduled = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByScheduledOnRange(DateTime $gte = null, DateTime $lte = null): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByScheduledToRange(DateTime $gte = null, DateTime $lte = null): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByParticipantsIds(array $values, bool $contains = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByPoisIds(array $values, bool $contains = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByTypes(array $values, bool $contains = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByStatuses(array $values, bool $contains = true): SessionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByReferencable(bool $referencable = true): SessionRepositoryContract
    {
        return $this;
    }
}
