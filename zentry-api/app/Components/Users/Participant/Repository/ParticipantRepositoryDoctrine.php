<?php

namespace App\Components\Users\Participant\Repository;

use App\Components\Users\Participant\ParticipantContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;

/**
 * Class ParticipantRepositoryDoctrine
 *
 * @package App\Components\Users\Participant\Repository
 */
class ParticipantRepositoryDoctrine extends AbstractRepository implements ParticipantRepositoryContract
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
    public function byIdentity(Identity $identity): ParticipantContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof ParticipantContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?ParticipantContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(ParticipantContract $entity): ParticipantContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(ParticipantContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.identity IN (:ids)")->setParameter('ids', $ids);
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.identity NOT IN (:ids)")->setParameter('ids', $ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUserIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        $userAlias = $this->join('user');

        if ($contains) {
            $this->builder()->andWhere("{$userAlias}.identity IN (:uIds)")->setParameter('uIds', $ids);
        } else {
            $this->builder()->andWhere("{$userAlias}.identity NOT IN (:uIds)")->setParameter('uIds', $ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmails(array $emails, bool $contains = true): ParticipantRepositoryContract
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
    public function filterByTeamIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        $teamAlias = $this->join('team', null, self::LEFT_JOIN_TYPE);
        $this->builder()->setParameter('tIds', $ids);

        if ($contains) {
            $this->builder()->andWhere("{$teamAlias}.identity IN (:tIds)");
        } else {
            $this->builder()->andWhere($this->builder()->expr()->orX()->addMultiple(
                [
                    $this->builder()->expr()->notIn("{$teamAlias}.identity", ":tIds"),
                    $this->builder()->expr()->isNull("{$teamAlias}.identity")
                ]
            ));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySchoolIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        $schoolAlias = $this->join('school', null, self::LEFT_JOIN_TYPE);
        $this->builder()->setParameter('sIds', $ids);

        if ($contains) {
            $this->builder()->andWhere("{$schoolAlias}.identity IN (:sIds)");
        } else {
            $this->builder()->andWhere($this->builder()->expr()->orX()->addMultiple(
                [
                    $this->builder()->expr()->notIn("{$schoolAlias}.identity", ":sIds"),
                    $this->builder()->expr()->isNull("{$schoolAlias}.identity")
                ]
            ));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByGoalsIds(array $ids, bool $contains = true): ParticipantRepositoryContract
    {
        $goalAlias = $this->join('goals', null, self::INNER_JOIN_TYPE, true);

        if ($contains) {
            $this->builder()->andWhere("{$goalAlias}.identity IN (:gIds)")->setParameter('gIds', $ids);
        } else {
            $this->builder()->andWhere("{$goalAlias}.identity NOT IN (:gIds)")->setParameter('gIds', $ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isExists(string $email): bool
    {
        $this->builder()->andWhere("{$this->getAlias()}.email = :email")->setParameter('email', $email);

        $isExist = $this->getOne() instanceof ParticipantContract;

        $this->refreshBuilder();

        return $isExist;
    }
}
