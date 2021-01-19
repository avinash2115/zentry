<?php

namespace App\Components\Sessions\Session\Repository;

use App\Components\Sessions\Session\SessionContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class SessionRepositoryDoctrine
 *
 * @package App\Components\Sessions\Session\Repository
 */
class SessionRepositoryDoctrine extends AbstractRepository implements SessionRepositoryContract
{
    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS);
    }

    /**
     * @inheritdoc
     */
    public function byIdentity(Identity $identity): SessionContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof SessionContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?SessionContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(SessionContract $entity): SessionContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(SessionContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $values, bool $contains = true): SessionRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.identity IN (:ids)");
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.identity NOT IN (:ids)");
        }

        $this->builder()->setParameter('ids', $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $values, bool $contains = true): SessionRepositoryContract
    {
        $userAlias = $this->join('user');

        if ($contains) {
            $this->builder()->andWhere("{$userAlias}.identity IN (:uIds)");
        } else {
            $this->builder()->andWhere("{$userAlias}.identity NOT IN (:uIds)");
        }

        $this->builder()->setParameter('uIds', $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByStarted(bool $isStarted = true): SessionRepositoryContract
    {
        if ($isStarted) {
            $this->builder()->andWhere("{$this->getAlias()}.startedAt IS NOT NULL");
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.startedAt IS NULL");
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByNullableEndedAt(): SessionRepositoryContract
    {
        $this->builder()->andWhere("{$this->getAlias()}.endedAt IS NULL");

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEnded(bool $isEnded = true): SessionRepositoryContract
    {
        if ($isEnded) {
            $this->builder()->andWhere("{$this->getAlias()}.endedAt IS NOT NULL");
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.endedAt IS NULL");
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByScheduledOn(bool $scheduled = true): SessionRepositoryContract
    {
        if ($scheduled) {
            $this->builder()->andWhere("{$this->getAlias()}.scheduledOn IS NULL");
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.scheduledOn IS NOT NULL");
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByScheduledOnRange(DateTime $gte = null, DateTime $lte = null): SessionRepositoryContract
    {
        if ($gte instanceof DateTime) {
            $this->builder()->andWhere("{$this->getAlias()}.scheduledOn >= :soGte")->setParameter('soGte', $gte);
        }

        if ($lte instanceof DateTime) {
            $this->builder()->andWhere("{$this->getAlias()}.scheduledOn <= :soLte")->setParameter('soLte', $lte);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByScheduledToRange(DateTime $gte = null, DateTime $lte = null): SessionRepositoryContract
    {
        if ($gte instanceof DateTime) {
            $this->builder()->andWhere("{$this->getAlias()}.scheduledTo >= :stGte")->setParameter('stGte', $gte);
        }

        if ($lte instanceof DateTime) {
            $this->builder()->andWhere("{$this->getAlias()}.scheduledTo <= :stLte")->setParameter('stLte', $lte);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByParticipantsIds(array $values, bool $contains = true): SessionRepositoryContract
    {
        $participantsAlias = $this->join('participants');

        if ($contains) {
            $this->builder()->andWhere("{$participantsAlias}.identity IN (:pIds)");
        } else {
            $this->builder()->andWhere("{$participantsAlias}.identity NOT IN (:pIds)");
        }

        $this->builder()->setParameter('pIds', $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByPoisIds(array $values, bool $contains = true): SessionRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->join('pois')}.identity IN (:poiIds)");
        } else {
            $this->builder()->andWhere("{$this->join('pois')}.identity NOT IN (:poiIds)");
        }

        $this->builder()->setParameter('poiIds', $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByTypes(array $values, bool $contains = true): SessionRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.type IN (:types)");
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.type NOT IN (:types)");
        }

        $this->builder()->setParameter('types', $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByStatuses(array $values, bool $contains = true): SessionRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.status IN (:statuses)");
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.status NOT IN (:statuses)");
        }

        $this->builder()->setParameter('statuses', $values);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByReferencable(bool $referencable = true): SessionRepositoryContract
    {
        if ($referencable) {
            $this->builder()->andWhere("{$this->getAlias()}.reference IS NOT NULL");
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.reference IS NULL");
        }

        return $this;
    }
}
