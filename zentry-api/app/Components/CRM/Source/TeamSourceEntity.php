<?php

namespace App\Components\CRM\Source;

use App\Components\Users\Team\TeamContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use UnexpectedValueException;

/**
 * Class TeamSourceEntity
 *
 * @package App\Components\CRM\Source
 */
class TeamSourceEntity extends SourceEntity
{
    /**
     * @var TeamContract
     */
    private TeamContract $entity;

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
        if (!$entity instanceof TeamContract) {
            throw new UnexpectedValueException('Wrong entity type');
        }
        $this->entity = $entity;

        return $this;
    }
}
