<?php

namespace App\Components\CRM\Source;

use App\Components\Users\Participant\ParticipantContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use UnexpectedValueException;

/**
 * Class ParticipantSourceEntity
 *
 * @package App\Components\CRM\Source
 */
class ParticipantSourceEntity extends SourceEntity
{
    /**
     * @var ParticipantContract
     */
    private ParticipantContract $entity;

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
        if (!$entity instanceof ParticipantContract) {
            throw new UnexpectedValueException('Wrong entity type');
        }
        $this->entity = $entity;

        return $this;
    }
}
