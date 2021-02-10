<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects;

use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Entity;
use Arr;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * Class District
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects
 */
class Provider implements Entity
{
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
    private string $code;

   

    /**
     * @var bool
     */
    private bool $isBillable;

    /**
     * @var bool
     */
    private bool $isTherapy;

    /**
     * @var bool
     */
    private bool $isEvaluation;

    /**
     * @var string
     */
    private string $attendanceType;

    /**
     * @var bool
     */
    private bool $active;

    /**
     * ProviderTransaction constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)Arr::get($args, 'id');

        $this->name = Arr::get($args, 'name', '');
        $this->code = Arr::get($args, 'code', '');
      

        $this->isBillable = filter_var(Arr::get($args, 'is_billable', false), FILTER_VALIDATE_BOOLEAN);
        $this->isTherapy = filter_var(Arr::get($args, 'is_therapy', false), FILTER_VALIDATE_BOOLEAN);
        $this->isEvaluation = filter_var(Arr::get($args, 'is_evaluation', false), FILTER_VALIDATE_BOOLEAN);

        $this->attendanceType = Arr::get($args, 'attendance_type', '');
        $this->active = filter_var(Arr::get($args, 'active', false), FILTER_VALIDATE_BOOLEAN);
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
    public function code(): string
    {
        return $this->code;
    }

   

    /**
     * @return bool
     */
    public function isBillable(): bool
    {
        return $this->isBillable;
    }

    /**
     * @return bool
     */
    public function isTherapy(): bool
    {
        return $this->isTherapy;
    }

    /**
     * @return bool
     */
    public function isEvaluation(): bool
    {
        return $this->isEvaluation;
    }

    /**
     * @return string
     */
    public function attendanceType(): string
    {
        return $this->attendanceType;
    }

    /**
     * @return bool
     */
    public function active(): bool
    {
        return $this->active;
    }
}
