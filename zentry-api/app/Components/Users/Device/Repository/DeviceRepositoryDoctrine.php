<?php

namespace App\Components\Users\Device\Repository;

use App\Components\Users\Device\DeviceContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class DeviceRepositoryDoctrine
 *
 * @package App\Components\Users\Device\Repository
 */
class DeviceRepositoryDoctrine extends AbstractRepository implements DeviceRepositoryContract
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
    public function byIdentity(Identity $identity): DeviceContract
    {
        $entity = $this->direct($identity);

        if (!$entity instanceof DeviceContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getOne(): ?DeviceContract
    {
        $result = $this->builder()->getQuery()->getOneOrNullResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function persist(DeviceContract $passwordReset): DeviceContract
    {
        $this->manager()->persist($passwordReset);

        return $passwordReset;
    }

    /**
     * @inheritdoc
     */
    public function destroy(DeviceContract $passwordReset): bool
    {
        $this->manager()->remove($passwordReset);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): DeviceRepositoryContract
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
    public function filterByReferences(array $references, bool $contains = true): DeviceRepositoryContract
    {
        if ($contains) {
            $this->builder()->andWhere("{$this->getAlias()}.reference IN (:references)")->setParameter(
                'references',
                $references
            );
        } else {
            $this->builder()->andWhere("{$this->getAlias()}.reference NOT IN (:references)")->setParameter(
                'references',
                $references
            );
        }

        return $this;
    }
}
