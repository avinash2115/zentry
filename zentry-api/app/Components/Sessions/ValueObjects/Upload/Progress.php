<?php

namespace App\Components\Sessions\ValueObjects\Upload;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Collection;

/**
 * Class Progress
 *
 * @package App\Components\Sessions\ValueObjects\Upload
 */
final class Progress implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var int
     */
    private int $progress;

    /**
     * @var string
     */
    private string $_type;

    /**
     * Progress constructor.
     *
     * @param int    $progress
     * @param string $type
     */
    public function __construct(int $progress, string $type = 'sessions_streams_upload_progress')
    {
        $this->progress = $progress;
        $this->id = IdentityGenerator::next()->toString();
        $this->_type = $type;
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'progress' => $this->progress,
        ];
    }
}
