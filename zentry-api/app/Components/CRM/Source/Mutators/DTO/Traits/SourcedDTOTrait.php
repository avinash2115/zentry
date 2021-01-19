<?php

namespace App\Components\CRM\Source\Mutators\DTO\Traits;

use Illuminate\Support\Collection;

/**
 * Trait SourcedDTOTrait
 *
 * @package App\Components\CRM\Source\Mutators\DTO\Traits
 */
trait SourcedDTOTrait
{
    /**
     * @var Collection
     */
    public Collection $sources;

    /**
     * @var bool
     */
    public bool $imported = false;

    /**
     * @var bool
     */
    public bool $exported = false;

    /**
     * @param bool $imported
     */
    public function fillImported(bool $imported): void
    {
        $this->imported = $imported;
    }

    /**
     * @param bool $exported
     */
    public function fillExported(bool $exported): void
    {
        $this->exported = $exported;
    }

    /**
     * @param Collection $sources
     */
    public function fillSources(Collection $sources): void
    {
        $this->sources = $sources;
    }
}
