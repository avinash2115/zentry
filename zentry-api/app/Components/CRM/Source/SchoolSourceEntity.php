<?php

namespace App\Components\CRM\Source;

use App\Components\Users\Team\School\SchoolContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use UnexpectedValueException;

/**
 * Class SchoolSourceEntity
 *
 * @package App\Components\CRM\Source
 */
class SchoolSourceEntity extends SourceEntity
{
    /**
     * @var SchoolContract
     */
    private SchoolContract $entity;

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
        if (!$entity instanceof SchoolContract) {
            throw new UnexpectedValueException('Wrong entity type');
        }
        $this->entity = $entity;

        return $this;
    }
}
