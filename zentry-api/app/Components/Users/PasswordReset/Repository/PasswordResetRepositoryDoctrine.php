<?php

namespace App\Components\Users\PasswordReset\Repository;

use App\Components\Users\PasswordReset\PasswordResetContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class PasswordResetRepositoryDoctrine
 *
 * @package App\Components\Users\PasswordReset\Repository
 */
class PasswordResetRepositoryDoctrine extends AbstractRepository implements PasswordResetRepositoryContract
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
    public function byIdentity(Identity $identity): PasswordResetContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof PasswordResetContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?PasswordResetContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(PasswordResetContract $passwordReset): PasswordResetContract
    {
        $this->manager()->persist($passwordReset);

        return $passwordReset;
    }

    /**
     * @inheritdoc
     */
    public function destroy(PasswordResetContract $passwordReset): bool
    {
        $this->manager()->remove($passwordReset);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): PasswordResetRepositoryContract
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
