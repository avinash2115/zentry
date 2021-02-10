<?php

namespace App\Assistants\CRM\Drivers\DTO\Service;

use App\Assistants\Transformers\JsonApi\Traits\IdTrait;
use App\Convention\Contracts\Arrayable;

/**
 * Class ServiceDTO
 *
 * @package App\Assistants\CRM\Drivers\DTO\Session
 */
class ServiceDTO implements Arrayable
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
            'category' => $this->category,
            'status' => $this->status,
            'actions' => $this->actions,

        ];
    }
}
