<?php

namespace App\Components\CRM\Source;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Services\Service\ServiceReadonlyContract;
use UnexpectedValueException;

/**
 * Class ServiceSourceEntity
 *
 * @package App\Components\CRM\Source
 */
class ServiceSourceEntity extends SourceEntity
{
    /**
     * @var ServiceReadonlyContract
     */
    private ServiceReadonlyContract $entity;

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
        if (!$entity instanceof ServiceReadonlyContract) {
            throw new UnexpectedValueException('Wrong entity type');
        }

        $this->entity = $entity;

        return $this;
    }
}
