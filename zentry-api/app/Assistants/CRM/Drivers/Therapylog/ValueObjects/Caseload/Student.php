<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Caseload;

use Arr;
use InvalidArgumentException;

/**
 * Class Student
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Caseload
 */
class Student
{
    public const GENDER_MALE = 'M';

    public const GENDER_FEMALE = 'F';

    public const GENDERS_AVAILABLE = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $firstName;

    /**
     * @var string
     */
    private string $lastName;

    /**
     * @var string
     */
    private string $birthDate;

    /**
     * @var string
     */
    private string $gender;

    /**
     * @var int
     */
    private int $districtId;

    /**
     * Student constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (Arr::has($args, 'id') && !strEmpty(Arr::get($args, 'id'))) {
            $this->id = (int)Arr::get($args, 'id');
        } else {
            throw new InvalidArgumentException('ID must be present');
        }

        if (Arr::has($args, 'first_name') && !strEmpty(Arr::get($args, 'first_name'))) {
            $this->firstName = Arr::get($args, 'first_name');
        } else {
            throw new InvalidArgumentException('First name must be present');
        }

        if (Arr::has($args, 'last_name') && !strEmpty(Arr::get($args, 'last_name'))) {
            $this->lastName = Arr::get($args, 'last_name');
        } else {
            throw new InvalidArgumentException('Last name must be present');
        }

        if (Arr::has($args, 'district_id') && !strEmpty(Arr::get($args, 'district_id'))) {
            $this->districtId = (int)Arr::get($args, 'district_id');
        } else {
            throw new InvalidArgumentException('Last name must be present');
        }

        $this->birthDate = (string)Arr::get($args, 'birth_date');

        if (!in_array((string)Arr::get($args, 'gender'), self::GENDERS_AVAILABLE, true)) {
            $this->gender = self::GENDER_MALE;
        } else {
            $this->gender = (string)Arr::get($args, 'gender');
        }
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function birthDate(): string
    {
        return $this->birthDate;
    }

    /**
     * @return string
     */
    public function gender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     *
     * @return bool
     */
    public function isGender(string $gender): bool
    {
        return $this->gender() === $gender;
    }

    /**
     * @return int
     */
    public function districtId(): int
    {
        return $this->districtId;
    }
}
