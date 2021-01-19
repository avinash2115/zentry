<?php

namespace App\Components\CRM\Source\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Illuminate\Support\Collection;

/**
 * Trait HasSourceTrait
 *
 * @package App\Components\CRM\Source\Traits
 */
trait HasSourceTrait
{
    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $sources;

    /**
     * @inheritDoc
     */
    public function sources(): Collection
    {
        return $this->doctrineCollectionToCollection($this->sources);
    }

    /**
     *
     */
    private function setSources(): void
    {
        $this->sources = new ArrayCollection();
    }
}
