<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment;

use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Entity;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Service;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment\StudentAppointment\StudentAppointment;
use \Arr;
use Illuminate\Support\Collection;

/**
 * Class ServiceAppointment
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment
 */
class ServiceAppointment implements Entity
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $timeSpentType;

    /**
     * @var int
     */
    private int $minutesSpent;

    /**
     * @var Service
     */
    private Service $service;

    /**
     * @var Collection
     */
    private Collection $studentAppointments;

    /**
     * ServiceAppointment constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)Arr::get($args, 'id');
        $this->timeSpentType = Arr::get($args, 'time_spent_type', '');
        $this->minutesSpent = (int)Arr::get($args, 'minutes_spent');

        $this->service = new Service(Arr::get($args, 'service'));

        $this->studentAppointments = collect(Arr::get($args, 'student_appointments', []))->map(
            function (array $data) {
                return new StudentAppointment($data);
            }
        );
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
    public function timeSpentType(): string
    {
        return $this->timeSpentType;
    }

    /**
     * @return int
     */
    public function minutesSpent(): int
    {
        return $this->minutesSpent;
    }

    /**
     * @return Service
     */
    public function service(): Service
    {
        return $this->service;
    }

    /**
     * @return Collection
     */
    public function studentAppointments(): Collection
    {
        return $this->studentAppointments;
    }
}
