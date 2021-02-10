<?php

namespace App\Assistants\CRM\Drivers\DTO\Team;

use App\Assistants\Transformers\JsonApi\Traits\IdTrait;

/**
 * Class TeamDTO
 *
 * @package App\Assistants\CRM\Drivers\DTO\Team
 */
class TeamDTO
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
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
