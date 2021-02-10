<?php

namespace App\Components\Share\Shared\Repository;

use App\Components\Share\Shared\SharedContract;
use App\Components\Share\ValueObjects\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class SharedRepositoryDoctrine
 *
 * @package App\Components\Share\Shared\Repository
 */
class SharedRepositoryDoctrine extends AbstractRepository implements SharedRepositoryContract
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
    public function byIdentity(Identity $identity): SharedContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof SharedContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?SharedContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(SharedContract $entity): SharedContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function destroy(SharedContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterById(array $ids, bool $contains = true): SharedRepositoryContract
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
    public function filterByPayload(Payload $payload): SharedRepositoryContract
    {
        $this->builder()
            ->andWhere("JSON_CONTAINS({$this->getAlias()}.payload, :payloadJson) = 1")
            ->setParameter('payloadJson', json_encode($payload->toArray()));

        return $this;
    }
}
