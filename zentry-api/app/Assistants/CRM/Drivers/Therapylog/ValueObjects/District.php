<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects;

use Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class District
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects
 */
class District
{
    public const DISTRICT_SIGNATURE_NOT_REQUIRED = 'Not required';

    public const DISTRICT_SIGNATURE_ALWAYS = 'Always';

    public const DISTRICT_SIGNATURE_BILLABLE = 'Billable';

    public const AVAILABLE_DISTRICT_SIGNATURES = [
        self::DISTRICT_SIGNATURE_NOT_REQUIRED => self::DISTRICT_SIGNATURE_NOT_REQUIRED,
        self::DISTRICT_SIGNATURE_ALWAYS => self::DISTRICT_SIGNATURE_ALWAYS,
        self::DISTRICT_SIGNATURE_BILLABLE => self::DISTRICT_SIGNATURE_BILLABLE,
    ];

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $signatureRequired;

    /**
     * @var bool
     */
    private bool $therapyComponentEnabled;

    /**
     * @var Collection|District[]
     */
    private Collection $districts;

    /**
     * District constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (Arr::has($args, 'id') && !strEmpty(Arr::get($args, 'id'))) {
            $this->id = Arr::get($args, 'id');
        } else {
            throw new InvalidArgumentException('ID must be present');
        }

        if (Arr::has($args, 'name') && !strEmpty(Arr::get($args, 'name'))) {
            $this->name = Arr::get($args, 'name');
        } else {
            throw new InvalidArgumentException('Name can`t be empty');
        }

        if (Arr::has($args, 'signature_required') && !strEmpty(Arr::get($args, 'signature_required')) && Arr::has(
                self::AVAILABLE_DISTRICT_SIGNATURES,
                Arr::get($args, 'signature_required')
            )) {
            $this->signatureRequired = Arr::get($args, 'signature_required');
        } else {
            throw new InvalidArgumentException('District signature filed must be valid');
        }

        if (Arr::has($args, 'therapy_component_enabled')) {
            if (filter_var(Arr::get($args, 'therapy_component_enabled'), FILTER_VALIDATE_BOOLEAN)) {
                $this->therapyComponentEnabled = Arr::get($args, 'therapy_component_enabled');
            } else {
                throw new InvalidArgumentException('Name can`t be empty');
            }
        }

        if (Arr::has($args, 'districts')) {
            $this->districts = collect(Arr::get($args, 'districts'))->map(fn($district) => new District($district));
        } else {
            $this->districts = collect([]);
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
     * @return string
     */
    public function signatureRequired(): string
    {
        return $this->signatureRequired;
    }

    /**
     * @return bool
     */
    public function therapyComponentEnabled(): bool
    {
        return $this->therapyComponentEnabled;
    }

    /**
     * @return Collection
     */
    public function districts(): Collection
    {
        return $this->districts;
    }
}
