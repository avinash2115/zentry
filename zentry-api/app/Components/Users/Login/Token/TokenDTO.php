<?php

namespace App\Components\Users\Login\Token;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\Login\Token\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class TokenDTO
 *
 * @package App\Components\Users\Login\Token
 */
class TokenDTO implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    public string $createdAt;

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
                'created_at' => $this->createdAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'createdAt' => $this->createdAt,
        ];
    }
}
