<?php

namespace App\Components\CRM\SyncLog\Repository;

use App\Components\CRM\SyncLog\SyncLogContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;

/**
 * Class SyncLogRepositoryDoctrine
 *
 * @package App\Components\CRM\SyncLog\Repository
 */
class SyncLogRepositoryDoctrine extends AbstractRepository implements SyncLogRepositoryContract
{
    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS);
    }

    /**
     * @inheritdoc
     */
    public function byIdentity(Identity $identity): SyncLogContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof SyncLogContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?SyncLogContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(SyncLogContract $entity): SyncLogContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(SyncLogContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByCRM(string $id, bool $contains = true): SyncLogRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.crm IN (:crm)")->setParameter('crm', $id);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.crm NOT IN (:crm)")->setParameter('crm', $id);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByTypes(array $ids, bool $contains = true): SyncLogRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.type IN (:types)")->setParameter('types', $ids);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.type NOT IN (:types)")->setParameter('types', $ids);
        }

        return $this;
    }
}