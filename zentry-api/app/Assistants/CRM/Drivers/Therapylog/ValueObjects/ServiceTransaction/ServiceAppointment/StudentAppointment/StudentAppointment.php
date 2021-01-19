<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment\StudentAppointment;

use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Entity;
use \Arr;

/**
 * Class StudentAppointment
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment
 */
class StudentAppointment implements Entity
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var Student
     */
    private Student $student;

    /**
     * StudentAppointment constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)Arr::get($args, 'id');

        $this->student = new Student(Arr::get($args, 'student', []));
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return Student
     */
    public function student(): Student
    {
        return $this->student;
    }
}
