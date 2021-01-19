<?php

namespace App\Components\CRM\Source;

use App\Components\CRM\Contracts\CRMImportableContract;

/**
 * Interface SourceReadonlyContract
 *
 * @package App\Components\CRM\Source
 */
interface SourceContract extends SourceReadonlyContract
{

    /**
     * @param CRMImportableContract $entity
     *
     * @return SourceContract
     */
    public function setOwner($entity): SourceContract;
}
