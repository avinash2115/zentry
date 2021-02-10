<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API;

use Illuminate\Support\Collection;

/**
 * Class Records
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction
 */
class Records
{
    /**
     * @var Collection|Entity[]
     */
    private Collection $entities;

    /**
     * @var Meta
     */
    private Meta $meta;

    /**
     * Records constructor.
     *
     * @param Collection $entities
     * @param Meta $meta
     */
    public function __construct(Collection $entities, Meta $meta)
    {
        $this->entities = $entities;
        $this->meta = $meta;
    }

    /**
     * @return Collection|Entity[]
     */
    public function entities(): Collection
    {
        return $this->entities;
    }

    /**
     * @return Meta
     */
    public function meta(): Meta
    {
        return $this->meta;
    }

}
