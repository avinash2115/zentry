<?php

namespace App\Components\Services\Service\Mutators\DTO;

use App\Components\CRM\Source\Mutators\DTO\Traits\SourceMutatorTrait;
use App\Components\Services\Service\ServiceDTO;
use App\Components\Services\Service\ServiceReadonlyContract;
use App\Components\Users\User\Mutators\DTO\Mutator as UserMutator;
use App\Components\Users\User\Mutators\DTO\Traits\MutatorTrait as UserMutatorTrait;
use App\Convention\DTO\Mutators\SimplifiedDTOContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Mutator
 */
final class Mutator implements SimplifiedDTOContract
{
    use SimplifiedDTOTrait;
    use SourceMutatorTrait;
    use UserMutatorTrait;

    public const TYPE = 'services';

    /**
     * @param ServiceReadonlyContract $entity
     *
     * @return ServiceDTO
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function toDTO(ServiceReadonlyContract $entity): ServiceDTO
    {
        $dto = new ServiceDTO();
        $dto->id = $entity->identity()->toString();
        $dto->name = $entity->name();
        $dto->code = $entity->code();
        $dto->category = $entity->category();
        $dto->status = $entity->status();
        $dto->actions = $entity->actions();



        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $this->userMutator__()->simplifiedMutation();

        $dto->user = $this->userMutator__()->toDTO($entity->user());

        $this->fill($dto, $entity);

        return $dto;
    }
}
