<?php

namespace App\Components\CRM\Source;

use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use UnexpectedValueException;

/**
 * Class ParticipantGoalSourceEntity
 *
 * @package App\Components\CRM\Source
 */
class ParticipantGoalSourceEntity extends SourceEntity
{
    /**
     * @var GoalReadonlyContract
     */
    private GoalReadonlyContract $entity;

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
        if (!$entity instanceof GoalReadonlyContract) {
            throw new UnexpectedValueException('Wrong entity type');
        }

        $this->entity = $entity;

        return $this;
    }
}
