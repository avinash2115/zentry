<?php

namespace App\Components\CRM\Source;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Providers\ProviderService\ProviderReadonlyContract;
use UnexpectedValueException;

/**
 * Class ProviderSourceEntity
 *
 * @package App\Components\CRM\Source
 */
class ProviderSourceEntity extends SourceEntity
{
    /**
     * @var ProviderReadonlyContract
     */
    private ProviderReadonlyContract $entity;

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
        if (!$entity instanceof ProviderReadonlyContract) {
            throw new UnexpectedValueException('Wrong entity type');
        }

        $this->entity = $entity;

        return $this;
    }
}
