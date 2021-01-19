<?php

namespace App\Convention\Entities\Traits;

/**
 * Trait DirtiableTrait
 *
 * @package App\Convention\Entities\Traits
 */
trait DirtiableTrait
{
    /**
     * @var bool
     */
    private bool $isDirty = false;

    /**
     * @inheritDoc
     */
    private function dirty(): void
    {
        $this->isDirty = true;
    }

    /**
     * @inheritDoc
     */
    public function isDirty(): bool
    {
        return $this->isDirty;
    }
}
