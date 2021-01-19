<?php

namespace App\Assistants\CRM\Drivers\DTO\Team\School;

use App\Assistants\Transformers\JsonApi\Traits\IdTrait;

/**
 * Class SchoolDTO
 *
 * @package App\Assistants\CRM\Drivers\DTO\Team\School
 */
class SchoolDTO
{
    use IdTrait;

    /**
     * @var string
     */
    public ?string $districtId = null;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var int
     */
    public int $available;

    /**
     * @var bool
     */
    public bool $home;

    /**
     * @var string|null
     */
    public ?string $streetAddress = null;

    /**
     * @var string|null
     */
    public ?string $city = null;

    /**
     * @var string|null
     */
    public ?string $state = null;

    /**
     * @var string|null
     */
    public ?string $zip = null;

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'home' => $this->home,
            'districtId' => $this->districtId,
            'available' => $this->available,
            'streetAddress' => $this->streetAddress,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
        ];
    }
}
