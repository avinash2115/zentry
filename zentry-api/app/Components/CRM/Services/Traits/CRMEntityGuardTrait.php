<?php

namespace App\Components\CRM\Services\Traits;

use App\Components\CRM\Services\Source\Traits\SourceServiceTrait;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait CRMEntityGuardTrait
 *
 * @package App\Components\CRM\Services\Traits
 */
trait CRMEntityGuardTrait
{
    use SourceServiceTrait;

    /**
     * @param CRMImportableContract $entity
     *
     * @throws PermissionDeniedException
     * @throws BindingResolutionException
     */
    final protected function checkRemoving(CRMImportableContract $entity): void
    {
        if ($this->authService__()->check()) {
            $this->sourceService__()->applyFilters(
                [
                    'type' => [
                        'className' => $entity->sourceEntityClass(),
                        'has' => true,
                    ],
                    'owners' => [
                        'collection' => [$entity->identity()->toString()],
                        'has' => true,
                    ],
                ]
            );

            if ($this->sourceService__()->listRO()->isNotEmpty()) {
                throw new PermissionDeniedException('Not allowed remove imported entity from CRM');
            }
        }
    }
}
