<?php

namespace App\Components\CRM\Source\Mutators\DTO\Traits;

use App\Components\CRM\Source\Mutators\DTO\Contracts\SourcedDTOContract;
use App\Components\CRM\Source\Mutators\DTO\Mutator;
use App\Components\CRM\Source\SourceReadonlyContract;
use App\Components\CRM\Contracts\CRMImportableContract;

/**
 * Trait SourceMutatorTrait
 *
 * @package App\Components\CRM\Source\Mutators\DTO\Traits
 */
trait SourceMutatorTrait
{
    /**
     * @param SourcedDTOContract    $dto
     * @param CRMImportableContract $entity
     */
    public function fill(SourcedDTOContract $dto, CRMImportableContract $entity): void
    {
        $dto->fillImported(
            $entity->sources()->filter(
                fn(SourceReadonlyContract $source) => $source->direction() === SourceReadonlyContract::DIRECTION_IN
            )->isNotEmpty()
        );

        $dto->fillExported(
            $entity->sources()->filter(
                fn(SourceReadonlyContract $source) => $source->direction() === SourceReadonlyContract::DIRECTION_OUT
            )->isNotEmpty()
        );

        $dto->fillSources(
            $entity->sources()->map(
                function (SourceReadonlyContract $source) {
                    return app()->make(Mutator::class)->toDTO($source);
                }
            )
        );
    }
}
