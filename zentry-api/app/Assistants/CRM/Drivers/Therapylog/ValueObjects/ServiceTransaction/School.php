<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction;

use \Arr;
use \InvalidArgumentException;

/**
 * Class School
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction
 */
class School
{

    public const STATUS_ACTIVE   = 'ACTIVE';

    public const STATUS_INACTIVE = 'INACTIVE';

    public const AVAILABLE_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    /**
     * @var int
     */
    private int $id;

    /**
     * @var int
     */
    private int $districtId;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var string|null
     */
    private ?string $streetAddress = null;

    /**
     * @var string|null
     */
    private ?string $city = null;

    /**
     * @var string|null
     */
    private ?string $state = null;

    /**
     * @var string|null
     */
    private ?string $zip = null;

    /**
     * School constructor.
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

        if (Arr::has($args, 'name') && !strEmpty(Arr::get($args, 'name'))) {
            $this->name = Arr::get($args, 'name');
        } else {
            throw new InvalidArgumentException('Name must be present');
        }

        if (Arr::has($args, 'district_id') && !strEmpty(Arr::get($args, 'district_id'))) {
            $this->districtId = (int)Arr::get($args, 'district_id');
        } else {
            throw new InvalidArgumentException('district_id must be present');
        }

        if (
            Arr::has($args, 'status') &&
            !strEmpty(Arr::get($args, 'status')) &&
            Arr::has(array_flip(self::AVAILABLE_STATUSES), Arr::get($args, 'status'))
        ) {
            $this->status = Arr::get($args, 'status');
        } else {
            throw new InvalidArgumentException('district_id must be present');
        }

        if (Arr::has($args, 'street_address') && !strEmpty(Arr::get($args, 'street_address'))) {
            $this->streetAddress = Arr::get($args, 'street_address');
        }

        if (Arr::has($args, 'city') && !strEmpty(Arr::get($args, 'city'))) {
            $this->city = Arr::get($args, 'city');
        }

        if (Arr::has($args, 'state') && !strEmpty(Arr::get($args, 'state'))) {
            $this->state = Arr::get($args, 'state');
        }

        if (Arr::has($args, 'zip') && !strEmpty(Arr::get($args, 'zip'))) {
            $this->zip = Arr::get($args, 'zip');
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
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function districtId(): int
    {
        return $this->districtId;
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status() === self::STATUS_ACTIVE;
    }

    /**
     * @return string|null
     */
    public function streetAddress(): ?string
    {
        return $this->streetAddress;
    }

    /**
     * @return string|null
     */
    public function city(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function state(): ?string
    {
        return $this->state;
    }

    /**
     * @return string|null
     */
    public function zip(): ?string
    {
        return $this->zip;
    }

}
