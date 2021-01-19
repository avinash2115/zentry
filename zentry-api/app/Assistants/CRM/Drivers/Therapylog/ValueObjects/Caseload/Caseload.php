<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Caseload;

use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Entity;
use Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class Caseload
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Caseload
 */
class Caseload implements Entity
{
    public const TIME_PERIOD_DAY = 'DAY';

    public const TIME_PERIOD_WEEK = 'WEEK';

    public const TIME_PERIOD_BIWEEK = 'BIWEEK';

    public const TIME_PERIOD_MONTH = 'MONTH';

    public const TIME_PERIOD_TRIMESTER = 'TRIMESTER';

    public const ELIGIBILITY_TYPE_TODAY = 'TODAY';

    public const ELIGIBILITY_TYPE_ONE_TIME = 'ONE TIME';

    public const ELIGIBILITY_TYPE_ANNUAL = 'ANNUAL';

    public const ELIGIBILITY_TYPE_DENIED = 'DENIED';

    public const ELIGIBILITY_TYPE_INELIGIBLE = 'INELIGIBLE';

    /**
     * @var int
     */
    private int $id;

    /**
     * @var int
     */
    private int $minutes;

    /**
     * @var string
     */
    private string $timePeriod;

    /**
     * @var string
     */
    private string $eligibilityType;

    /**
     * @var Collection
     */
    private Collection $students;

    /**
     * Caseload constructor.
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

        $this->minutes = Arr::get($args, 'minutes', 0) === null ? 0 : Arr::get($args, 'minutes', 0);
        $this->timePeriod = Arr::get($args, 'time_period', self::TIME_PERIOD_DAY);
        $this->eligibilityType = Arr::get($args, 'eligibility_type', self::ELIGIBILITY_TYPE_TODAY);

        if (Arr::has($args, 'student')) {
            $this->students = collect([]);
            if (Arr::has($args['student'], 'id')) {
                Arr::set($args, 'student', [Arr::get($args, 'student')]);
            }
            collect(Arr::get($args, 'student'))->map(fn($item) => $this->students->push(new Student($item)));
        } else {
            throw new InvalidArgumentException('Student must be present');
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
     * @return int
     */
    public function minutes(): int
    {
        return $this->minutes;
    }

    /**
     * @return string
     */
    public function timePeriod(): string
    {
        return $this->timePeriod;
    }

    /**
     * @return string
     */
    public function eligibilityType(): string
    {
        return $this->eligibilityType;
    }

    /**
     * @return Collection|Student[]
     */
    public function students(): Collection
    {
        return $this->students;
    }
}
