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
class Service implements Entity
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
     * @var string
     */
    private string $category;

     /**
     * @var string
     */
    private string $status;

     /**
     * @var string
     */
    private string $actions;


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
     * ServiceTransaction constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)Arr::get($args, 'id');

        $this->name = Arr::get($args, 'name', '');
        $this->code = Arr::get($args, 'code', '');
        $this->category = Arr::get($args, 'category', '');
        $this->status = Arr::get($args, 'status', '');
        $this->actions = Arr::get($args, 'actions', '');


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
     * @return string
     */
    public function category(): string
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function actions(): string
    {
        return $this->actions;
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
