<?php

namespace App\Components\Sessions\ValueObjects\Stream;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Collection;

/**
 * Class Token
 *
 * @package App\Components\Sessions\ValueObjects\Stream
 */
final class Token implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    public string $token;

    /**
     * @var string
     */
    protected string $_type = 'sessions_streams_token';

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->id = IdentityGenerator::next()->toString();
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
            'token' => $this->token,
        ];
    }
}
