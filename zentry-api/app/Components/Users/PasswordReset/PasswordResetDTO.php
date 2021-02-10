<?php

namespace App\Components\Users\PasswordReset;

use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\PasswordReset\Mutators\DTO\Mutator;
use App\Components\Users\User\UserDTO;
use Illuminate\Support\Collection;

/**
 * Class PasswordResetDTO
 *
 * @package App\Components\Users\PasswordReset
 */
class PasswordResetDTO implements PresenterContract, RelationshipsContract
{
    use PresenterTrait;

    public const ROUTE_NAME_SHOW = 'password_reset.show';

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $updatedAt;

    /**
     * @var UserDTO
     */
    public UserDTO $user;

    /**
     * @var string
     */
    public string $_type = Mutator::TYPE;

    /**
     * @var string
     */
    public string $route = self::ROUTE_NAME_SHOW;

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        return collect(
            [
                'user' => $this->user,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return $this->nested();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'user' => $this->user->toArray(),
        ];
    }
}
