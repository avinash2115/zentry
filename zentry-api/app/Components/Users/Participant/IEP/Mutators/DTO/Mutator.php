<?php

namespace App\Components\Users\Participant\IEP\Mutators\DTO;

use App\Components\Users\Participant\IEP\IEPDTO;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourceMutatorTrait;
use InvalidArgumentException;

/**
 * Class Mutator
 *
 * @package App\Components\Users\Participant\IEP\Mutators\DTO
 */
final class Mutator
{
    use SourceMutatorTrait;
    use SimplifiedDTOTrait;

    public const TYPE = 'users_participants_ieps';

    /**
     * @param IEPReadonlyContract $entity
     *
     * @return IEPDTO
     * @throws InvalidArgumentException
     */
    public function toDTO(IEPReadonlyContract $entity): IEPDTO
    {
        $dto = new IEPDTO();

        $dto->id = $entity->identity()->toString();

        $dto->dateActual = dateTimeFormatted($entity->dateActual());
        $dto->dateReeval = dateTimeFormatted($entity->dateReeval());

        $this->fill($dto, $entity);

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
