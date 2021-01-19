<?php

namespace App\Components\CRM\SyncLog;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\CRM\SyncLog\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class SyncLogDTO
 *
 * @package App\Components\CRM\SyncLog
 */
class SyncLogDTO implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    public string $syncLogType;

    /**
     * @var string|null
     */
    public ?string $createdAt;

    /**
     * @var string
     */
    public string $_type = Mutator::TYPE;

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'type' => $this->syncLogType,
                'created_at' => $this->createdAt,
            ]
        );
    }
}
