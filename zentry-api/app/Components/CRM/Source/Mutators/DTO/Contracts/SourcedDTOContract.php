<?php

namespace App\Components\CRM\Source\Mutators\DTO\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface SourcedDTOContract
 *
 * @package App\Components\CRM\Source\Mutators\DTO\Contracts
 */
interface SourcedDTOContract
{
    /**
     * @param bool $imported
     */
    public function fillImported(bool $imported): void;

    /**
     * @param bool $exported
     */
    public function fillExported(bool $exported): void;

    /**
     * @param Collection $sources
     */
    public function fillSources(Collection $sources): void;
}
