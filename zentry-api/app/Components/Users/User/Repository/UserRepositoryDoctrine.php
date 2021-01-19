<?php

namespace App\Components\Users\User\Repository;

use App\Components\Users\User\UserContract;
use App\Components\Users\User\UserEntity;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class UserRepositoryDoctrine
 *
 * @package App\Components\Users\User\Repository
 */
class UserRepositoryDoctrine extends AbstractRepository implements UserRepositoryContract
{
    /**
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS);
    }

    /**
     * @inheritDoc
     */
    public function byIdentity(Identity $identity): UserContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof UserContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?UserContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function persist(UserContract $user): UserContract
    {
        $this->manager()->persist($user);

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function destroy(UserContract $user): bool
    {
        $this->manager()->remove($user);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $ids, bool $contains = true): UserRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.identity IN (:ids)")->setParameter('ids', $ids);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.identity NOT IN (:ids)")->setParameter('ids', $ids);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filterByEmails(array $emails, bool $contains = true): UserRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.email IN (:emails)")->setParameter('emails', $emails);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.email NOT IN (:emails)")->setParameter('emails', $emails);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByStorageDrivers(
        array $drivers,
        bool $contains = true,
        bool $enabled = true
    ): UserRepositoryContract {
        $storageAlias = $this->join('storages', null, self::INNER_JOIN_TYPE, true);

        if ($contains) {
            $this->builder()->andWhere("{$storageAlias}.driver IN (:drivers)")->setParameter('drivers', $drivers);
        } else {
            $this->builder()->andWhere("{$storageAlias}.driver NOT IN (:drivers)")->setParameter('drivers', $drivers);
        }

        $this->builder()->andWhere("{$storageAlias}.enabled = (:enabled)")->setParameter('enabled', $enabled);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByDataProviders(array $drivers, bool $contains = true): UserRepositoryContract
    {
        $alias = $this->join('dataProviders');

        if ($contains) {
            $this->builder()->andWhere("{$alias}.driver IN (:drivers)")->setParameter('drivers', $drivers);
        } else {
            $this->builder()->andWhere("{$alias}.driver NOT IN (:drivers)")->setParameter('drivers', $drivers);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByDataProvidersStatuses(array $statuses, bool $contains = true): UserRepositoryContract
    {
        $alias = $this->join('dataProviders');

        if ($contains) {
            $this->builder()->andWhere("{$alias}.status IN (:statuses)")->setParameter('statuses', $statuses);
        } else {
            $this->builder()->andWhere("{$alias}.status NOT IN (:statuses)")->setParameter('statuses', $statuses);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isExists(string $email): bool
    {
        $this->builder()->andWhere("{$this->getAlias()}.email = :email")->setParameter('email', $email);

        $isExist = $this->getOne() instanceof UserContract;

        $this->refreshBuilder();

        return $isExist;
    }
}
