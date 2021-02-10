<?php

namespace App\Components\Users\Device\Repository;

use App\Components\Users\Device\DeviceContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class DeviceRepositoryMemory
 *
 * @package App\Components\Users\Device\Repository
 */
class DeviceRepositoryMemory extends AbstractMemoryRepository implements DeviceRepositoryContract
{
    /**
     */
    public function __construct()
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS);
    }

    /**
     * @inheritDoc
     */
    public function byIdentity(Identity $identity): DeviceContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof DeviceContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?DeviceContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(DeviceContract $passwordReset): DeviceContract
    {
        $this->register($passwordReset);

        return $passwordReset;
    }

    /**
     * @inheritDoc
     */
    public function register(DeviceContract $passwordReset): DeviceContract
    {
        $this->collector->put($passwordReset->identity()->toString(), $passwordReset);

        return $passwordReset;
    }

    /**
     * @inheritDoc
     */
    public function destroy(DeviceContract $passwordReset): bool
    {
        if ($this->collector->has($passwordReset->identity()->toString())) {
            $this->collector->forget($passwordReset->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): DeviceRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByReferences(array $references, bool $contains = true): DeviceRepositoryContract
    {
        return $this;
    }
}
