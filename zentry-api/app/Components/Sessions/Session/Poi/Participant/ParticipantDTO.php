<?php

namespace App\Components\Sessions\Session\Poi\Participant;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Sessions\Session\Poi\Participant\Mutators\DTO\Mutator;
use App\Components\Users\Participant\ParticipantDTO as UsersParticipantDTO;
use App\Components\Users\Participant\Traits\AudiencableDTOTrait;
use App\Convention\ValueObjects\Tags;
use Illuminate\Support\Collection;

/**
 * Class ParticipantDTO
 *
 * @package App\Components\Sessions\Session\Poi\Participant
 */
class ParticipantDTO implements PresenterContract, RelationshipsContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    public ?string $email = null;

    /**
     * @var string
     */
    public ?string $firstName = null;

    /**
     * @var string
     */
    public ?string $lastName = null;

    /**
     * @var string|null
     */
    public ?string $phoneCode = null;

    /**
     * @var string|null
     */
    public ?string $phoneNumber = null;

    /**
     * @var string|null
     */
    public ?string $avatar = null;

    /**
     * @var string
     */
    public string $startedAt;

    /**
     * @var string
     */
    public string $endedAt;

    /**
     * @var UsersParticipantDTO
     */
    public UsersParticipantDTO $raw;

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
                'email' => $this->email,
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'phone_code' => $this->phoneCode,
                'phone_number' => $this->phoneNumber,
                'avatar' => $this->avatar,
                'started_at' => $this->startedAt,
                'ended_at' => $this->endedAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'phoneCode' => $this->phoneCode,
            'phoneNumber' => $this->phoneNumber,
            'avatar' => $this->avatar,
            'startedAt' => $this->startedAt,
            'endedAt' => $this->endedAt,
        ];
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        return collect(
            [
                'raw' => $this->raw,
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
}
