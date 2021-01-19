<?php

namespace App\Components\CRM\Source;

use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use UnexpectedValueException;

/**
 * Class SessionSourceEntity
 *
 * @package App\Components\CRM\Source
 */
class SessionSourceEntity extends SourceEntity
{
    /**
     * @var SessionReadonlyContract
     */
    private SessionReadonlyContract $entity;

    /**
     * @inheritDoc
     */
    public function owner(): CRMImportableContract
    {
        return $this->entity;
    }

    /**
     * @inheritDoc
     */
    public function setOwner($entity): SourceEntity
    {
        if (!$entity instanceof SessionReadonlyContract) {
            throw new UnexpectedValueException('Wrong entity type');
        }

        $this->entity = $entity;

        return $this;
    }
}
