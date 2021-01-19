<?php

namespace App\Components\Users\Login\Token\Repository;

use App\Components\Users\Login\Token\TokenContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class TokenRepositoryDoctrine
 *
 * @package App\Components\Users\Login\Token\Repository
 */
class TokenRepositoryDoctrine extends AbstractRepository implements TokenRepositoryContract
{
    /**
     */
    public function __construct()
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS);
    }

    /**
     * @inheritdoc
     */
    public function byIdentity(Identity $identity): TokenContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof TokenContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?TokenContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(TokenContract $entity): TokenContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(TokenContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): TokenRepositoryContract
    {
        $userAlias = $this->join('user');

        if ($contains) {
            $this->builder()->andWhere("{$userAlias}.identity IN (:uIds)")->setParameter('uIds', $ids);
        } else {
            $this->builder()->andWhere("{$userAlias}.identity NOT IN (:uIds)")->setParameter('uIds', $ids);
        }

        return $this;
    }
}
