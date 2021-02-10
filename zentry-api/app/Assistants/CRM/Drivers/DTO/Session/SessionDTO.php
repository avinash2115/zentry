<?php

namespace App\Assistants\CRM\Drivers\DTO\Session;

use App\Assistants\CRM\Drivers\DTO\Service\ServiceDTO;
use App\Assistants\CRM\Drivers\DTO\Provider\ProviderDTO;
use App\Assistants\CRM\Drivers\DTO\Team\School\SchoolDTO;
use App\Assistants\Transformers\JsonApi\Traits\IdTrait;
use App\Convention\Contracts\Arrayable;
use Illuminate\Support\Collection;

/**
 * Class SessionDTO
 *
 * @package App\Assistants\CRM\Drivers\DTO\Session
 */
class SessionDTO implements Arrayable
{
    use IdTrait;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string|null
     */
    public ?string $scheduledOn;

    /**
     * @var string|null
     */
    public ?string $scheduledTo;

    /**
     * @var string|null
     */
    public ?string $type;

    /**
     * @var ServiceDTO|null
     */
    public ?ServiceDTO $service;

     /**
     * @var ProviderDTO|null
     */
    public ?ProviderDTO $provider;

    /**
     * @var SchoolDTO|null
     */
    public ?SchoolDTO $school;

    /**
     * @var Collection
     */
    public Collection $participants;

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'scheduledOn' => $this->scheduledOn,
            'scheduledTo' => $this->scheduledTo,
            'type' => $this->type,
            'service' => $this->service->toArray(),
            'provider' => $this->provider->toArray(),
            'school' => $this->school->toArray(),
            'participants' => $this->participants->toArray(),
        ];
    }
}
