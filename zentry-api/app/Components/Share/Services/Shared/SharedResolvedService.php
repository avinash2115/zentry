<?php

namespace App\Components\Share\Services\Shared;

use App\Components\Share\Shared\SharedReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class SharedResolvedService
 *
 * @package App\Components\Share\Services\Shared
 */
class SharedResolvedService
{
    /**
     * @var SharedReadonlyContract|null
     */
    private ?SharedReadonlyContract $entity = null;

    /**
     * @param SharedReadonlyContract $shared
     *
     * @return $this
     */
    public function workWithSharable(SharedReadonlyContract $shared): SharedResolvedService
    {
        $this->setEntity($shared);

        return $this;
    }

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity
    {
        return $this->_entity()->identity();
    }

    /**
     * @return SharedReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): SharedReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @param SharedReadonlyContract $entity
     *
     * @return SharedResolvedService
     */
    private function setEntity(SharedReadonlyContract $entity): SharedResolvedService
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return SharedReadonlyContract
     * @throws PropertyNotInit
     */
    private function _entity(): SharedReadonlyContract
    {
        if (!$this->entity instanceof SharedReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }
}
