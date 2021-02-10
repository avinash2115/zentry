<?php

namespace App\Components\Providers\ProviderService\Repository;

use App\Components\Providers\ProviderService\ProviderContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class ProviderRepositoryDoctrine
 *
 * @package App\Components\Providers\ProviderService\Repository
 */
class ProviderRepositoryDoctrine extends AbstractRepository implements ProviderRepositoryContract
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
    public function byIdentity(Identity $identity): ProviderContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof ProviderContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?ProviderContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(ProviderContract $entity): ProviderContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(ProviderContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $values, bool $contains = true): ProviderRepositoryContract
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
    public function filterByUsersIds(array $values, bool $contains = true): ProviderRepositoryContract
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
