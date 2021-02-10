<?php

namespace App\Assistants\CRM\Drivers\DTO\Provider;

use App\Assistants\Transformers\JsonApi\Traits\IdTrait;
use App\Convention\Contracts\Arrayable;

/**
 * Class ProviderDTO
 *
 * @package App\Assistants\CRM\Drivers\DTO\Sessions
 */
class ProviderDTO implements Arrayable
{
    use IdTrait;

    /**
     * @var string
     */
    public string $name;

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,

        ];
    }
}
