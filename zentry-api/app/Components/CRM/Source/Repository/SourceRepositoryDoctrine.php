<?php

namespace App\Components\CRM\Source\Repository;

use App\Components\CRM\Source\SourceContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;

/**
 * Class SourceRepositoryDoctrine
 *
 * @package App\Components\CRM\Source\Repository
 */
class SourceRepositoryDoctrine extends AbstractRepository implements SourceRepositoryContract
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
    public function byIdentity(Identity $identity): SourceContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof SourceContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?SourceContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(SourceContract $entity): SourceContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(SourceContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByCRM(string $id, bool $contains = true): SourceRepositoryContract
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
    public function filterByClass(string $className, bool $contains = true): SourceRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()} INSTANCE OF {$className}");
        } else {
            $this->builder()->andWhere("{$this->getAlias()} NOT INSTANCE OF :className")->setParameter('className', $className);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByDirections(array $ids, bool $contains = true): SourceRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.direction IN (:directions)")->setParameter('directions', $ids);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.direction NOT IN (:directions)")->setParameter('directions', $ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByOwnerIds(array $ids, bool $contains = true): SourceRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.ownerId IN (:ownerIds)")->setParameter('ownerIds', $ids);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.ownerId NOT IN (:ownerIds)")->setParameter('ownerIds', $ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySourceIds(array $ids, bool $contains = true): SourceRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.sourceId IN (:sourceIds)")->setParameter('sourceIds', $ids);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.sourceId NOT IN (:sourceIds)")->setParameter('sourceIds', $ids);
        }

        return $this;
    }
    
}