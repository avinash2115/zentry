<?php

namespace App\Components\Services\Service\Repository;

use App\Components\Services\Service\ServiceContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class ServiceRepositoryDoctrine
 *
 * @package App\Components\Services\Service\Repository
 */
class ServiceRepositoryDoctrine extends AbstractRepository implements ServiceRepositoryContract
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
    public function byIdentity(Identity $identity): ServiceContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof ServiceContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?ServiceContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(ServiceContract $entity): ServiceContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(ServiceContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $values, bool $contains = true): ServiceRepositoryContract
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
    public function filterByUsersIds(array $values, bool $contains = true): ServiceRepositoryContract
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
}
