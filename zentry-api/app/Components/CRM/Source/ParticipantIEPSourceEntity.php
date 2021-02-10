<?php

namespace App\Components\CRM\Source;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use UnexpectedValueException;

/**
 * Class ParticipantIEPSourceEntity
 *
 * @package App\Components\CRM\Source
 */
class ParticipantIEPSourceEntity extends SourceEntity
{
    /**
     * @var IEPReadonlyContract
     */
    private IEPReadonlyContract $entity;

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
        if (!$entity instanceof IEPReadonlyContract) {
            throw new UnexpectedValueException('Wrong entity type');
        }

        $this->entity = $entity;

        return $this;
    }
}
