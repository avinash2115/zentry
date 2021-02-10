<?php

namespace App\Components\Users\User\Profile;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\Profile\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class ProfileDTO
 *
 * @package App\Convention\Auth\User\Profile
 */
class ProfileDTO implements PresenterContract, LinksContract
{
    use PresenterTrait;

    public const ROUTE_NAME_SHOW = 'users.profile.show';

    /**
     * @var string
     */
    public string $firstName;

    /**
     * @var string
     */
    public string $lastName;

    /**
     * @var string|null
     */
    public ?string $phoneCode = null;

    /**
     * @var string|null
     */
    public ?string $phoneNumber = null;

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $updatedAt;

    /**
     * @var string
     */
    public string $_type = Mutator::TYPE;

    /**
     * @var string
     */
    public string $route = self::ROUTE_NAME_SHOW;

    /**
     * @var string
     */
    public string $routeParameterName = 'userId';

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phoneCode' => $this->phoneCode,
            'phoneNumber' => $this->phoneNumber,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * @return Collection
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'phone_code' => $this->phoneCode,
                'phone_number' => $this->phoneNumber,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
            ]
        );
    }

    /**
     * @return Collection
     */
    public function routeParameters(): Collection
    {
        return collect();
    }

    /**
     * @inheritDoc
     */
    public function data(LinkParameters $linkParameters): Collection
    {
        return collect(
            [
                'self' => route(
                    $this->route(),
                    [$this->routeParameterName => $linkParameters->stack()->get($this->routeParameterName)]
                ),
            ]
        );
    }
}
