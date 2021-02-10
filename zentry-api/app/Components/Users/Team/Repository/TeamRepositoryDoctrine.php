<?php

namespace App\Components\Users\Team\Repository;

use App\Components\Users\Team\TeamContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class TeamRepositoryDoctrine
 *
 * @package App\Components\Users\Team\Repository
 */
class TeamRepositoryDoctrine extends AbstractRepository implements TeamRepositoryContract
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
    public function byIdentity(Identity $identity): TeamContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof TeamContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?TeamContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(TeamContract $entity): TeamContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(TeamContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByMemberIds(array $ids, bool $contains = true): TeamRepositoryContract
    {
        $membersAlias = $this->join('members');

        if ($contains) {
            $this->builder()->andWhere("{$membersAlias}.identity IN (:mIds)")->setParameter('mIds', $ids);
        } else {
            $this->builder()->andWhere("{$membersAlias}.identity NOT IN (:mIds)")->setParameter('mIds', $ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByOwnerIds(array $ids, bool $contains = true): TeamRepositoryContract
    {
        $ownerAlias = $this->join('owner');

        if ($contains) {
            $this->builder()->andWhere("{$ownerAlias}.identity IN (:oIds)")->setParameter('oIds', $ids);
        } else {
            $this->builder()->andWhere("{$ownerAlias}.identity NOT IN (:oIds)")->setParameter('oIds', $ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySchoolIds(array $ids, bool $contains = true): TeamRepositoryContract
    {
        $alias = $this->join('schools');

        if ($contains) {
            $this->builder()->andWhere("{$alias}.identity IN (:sIds)")->setParameter('sIds', $ids);
        } else {
            $this->builder()->andWhere("{$alias}.identity NOT IN (:sIds)")->setParameter('sIds', $ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUserPresence(string $id): TeamRepositoryContract
    {
        $orX = $this->builder()->expr()->orX();

        $ownerAlias = $this->join('owner');
        $membersAlias = $this->join('members', null, self::LEFT_JOIN_TYPE);
        $requestUserAlias = $this->join('user', $this->join('requests', null, self::LEFT_JOIN_TYPE), self::LEFT_JOIN_TYPE);

        $orX->addMultiple(
            [
                $this->builder()->setParameter(':ownerId', $id)->expr()->eq(
                    "{$ownerAlias}.identity",
                    ':ownerId'
                ),
                $this->builder()->setParameter(':memberId', $id)->expr()->eq(
                    "{$membersAlias}.identity",
                    ':memberId'
                ),
                $this->builder()->setParameter(':requestUserId', $id)->expr()->eq(
                    "{$requestUserAlias}.identity",
                    ':requestUserId'
                ),
            ]
        );
        $this->builder()->andWhere($orX);

        return $this;
    }
}
