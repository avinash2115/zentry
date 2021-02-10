<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects;

use Illuminate\Support\Collection;

/**
 * Class Provider
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects
 */
class Provider
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var Collection
     */
    private Collection $districts;

    /**
     * Provider constructor.
     *
     * @param int   $id
     * @param array $districts
     */
    public function __construct(int $id, array $districts)
    {
        $this->id = $id;
        $this->districts = collect($districts)->map(fn($district) => new District($district));
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return Collection|District[]
     */
    public function districts(): Collection
    {
        return $this->districts;
    }
}
