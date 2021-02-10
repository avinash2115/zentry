<?php

namespace App\Components\Providers\ProviderService\Mutators\DTO;

use App\Components\CRM\Source\Mutators\DTO\Traits\SourceMutatorTrait;
use App\Components\Providers\ProviderService\ProviderDTO;
use App\Components\Providers\ProviderService\ProviderReadonlyContract;
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

    public const TYPE = 'providers';

    /**
     * @param ProviderReadonlyContract $entity
     *
     * @return ProviderDTO
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function toDTO(ProviderReadonlyContract $entity): ProviderDTO
    {
        $dto = new ProviderDTO();
        $dto->id = $entity->identity()->toString();
        $dto->name = $entity->name();
        $dto->code = $entity->code();


        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $this->userMutator__()->simplifiedMutation();

        $dto->user = $this->userMutator__()->toDTO($entity->user());

        $this->fill($dto, $entity);

        return $dto;
    }
}
