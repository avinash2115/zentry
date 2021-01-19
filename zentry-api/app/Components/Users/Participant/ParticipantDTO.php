<?php

namespace App\Components\Users\Participant;

use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\JsonApi\Traits\BasicLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Components\Users\Participant\Mutators\DTO\Mutator;
use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\Participant\Therapy\TherapyDTO;
use App\Components\Users\Team\School\SchoolDTO;
use App\Components\Users\Team\TeamDTO;
use App\Components\CRM\Source\Mutators\DTO\Contracts\SourcedDTOContract;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourcedDTOTrait;
use App\Components\Users\User\UserDTO;
use Illuminate\Support\Collection;

/**
 * Class ParticipantDTO
 *
 * @package App\Components\Users\Participant
 */
class ParticipantDTO implements PresenterContract, LinksContract, RelationshipsContract, SourcedDTOContract
{
    use PresenterTrait;
    use NestedLinksTrait;
    use SourcedDTOTrait;

    public const ROUTE_NAME_SHOW = 'users.participants.show';

    /**
     * @var string
     */
    public ?string $email;

    /**
     * @var string
     */
    public ?string $firstName;

    /**
     * @var string
     */
    public ?string $lastName;

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
    public ?string $avatar;

    /**
     * @var string|null
     */
    public ?string $gender;

    /**
     * @var string|null
     */
    public ?string $dob;

    /**
     * @var string|null
     */
    public ?string $parentEmail;

    /**
     * @var string|null
     */
    public ?string $parentPhoneNumber;

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
     * @var TherapyDTO
     */
    public TherapyDTO $therapy;

    /**
     * @var Collection
     */
    public Collection $goals;

    /**
     * @var Collection
     */
    public Collection $ieps;

    /**
     * @var TeamDTO
     */
    public ?TeamDTO $team = null;

    /**
     * @var SchoolDTO
     */
    public ?SchoolDTO $school = null;

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
    public string $routeParameterName = 'participantId';

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
                'gender' => $this->gender,
                'dob' => $this->dob,
                'parent_email' => $this->parentEmail,
                'parent_phone_number' => $this->parentPhoneNumber,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
                'imported' => $this->imported,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        $relationships = collect(
            [
                'user' => $this->user,
                'therapy' => $this->therapy,
                'sources' => $this->sources,
                'goals' => $this->goals,
                'ieps' => $this->ieps
            ]
        );

        if ($this->team instanceof TeamDTO) {
            $relationships->put('team', $this->team);
        }

        if ($this->school instanceof SchoolDTO) {
            $relationships->put('school', $this->school);
        }

        return $relationships;
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return collect();
    }
}
