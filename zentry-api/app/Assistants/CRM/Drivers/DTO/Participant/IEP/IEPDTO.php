<?php

namespace App\Assistants\CRM\Drivers\DTO\Participant\IEP;

use App\Assistants\Transformers\JsonApi\Traits\IdTrait;
use Illuminate\Support\Collection;

/**
 * Class IEPDTO
 *
 * @package App\Assistants\CRM\Drivers\DTO\Participant\IEP
 */
class IEPDTO
{
    use IdTrait;

    /**
     * @var string
     */
    public string $effectiveOn;

    /**
     * @var string
     */
    public string $reevalDate;

    /**
     * @var Collection
     */
    public Collection $goals;

    /**
     * @var array
     */
    public array $meta;

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'effective_on' => $this->effectiveOn,
            'reeval_date' => $this->reevalDate,
            'goals' => $this->goals->toArray(),
            'meta' => $this->meta,
        ];
    }
}
