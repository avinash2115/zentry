<?php

namespace App\Components\Users\User\Mutators\DTO;

use App\Components\Users\User\CRM\Mutators\DTO\Mutator as CRMMutator;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\Storage\Mutators\DTO\Mutator as StorageMutator;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\Poi\Mutators\DTO\Mutator as PoiMutator;
use App\Components\Users\User\Backtrack\Mutators\DTO\Mutator as BacktrackMutator;
use App\Components\Users\User\Profile\Mutators\DTO\Mutator as ProfileMutator;
use App\Components\Users\User\UserDTO;
use App\Components\Users\User\UserReadonlyContract;
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

    public const TYPE = 'users';

    /**
     * @var StorageMutator
     */
    private StorageMutator $storageMutator;

    /**
     * @var CRMMutator
     */
    private CRMMutator $crmMutator;

    public function __construct()
    {
        $this->storageMutator = app()->make(StorageMutator::class);
        $this->crmMutator = app()->make(CRMMutator::class);
    }

    /**
     * @param UserReadonlyContract $entity
     *
     * @return UserDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(UserReadonlyContract $entity): UserDTO
    {
        $dto = new UserDTO();

        $dto->id = $entity->identity()->toString();
        $dto->email = $entity->email();
        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());
        $dto->archivedAt = dateTimeFormatted($entity->archivedAt());

        $dto->poiDTO = app()->make(PoiMutator::class)->toDTO($entity->poi());
        $dto->backtrackDTO = app()->make(BacktrackMutator::class)->toDTO($entity->backtrack());
        $dto->profileDTO = app()->make(ProfileMutator::class)->toDTO($entity->profileReadonly());

        if (!$this->isSimplifiedMutation()) {
            $dto->storages = $entity->storages()->map(
                function (StorageReadonlyContract $storage) {
                    return $this->storageMutator->toDTO($storage);
                }
            );
        }

        return $dto;
    }
}
