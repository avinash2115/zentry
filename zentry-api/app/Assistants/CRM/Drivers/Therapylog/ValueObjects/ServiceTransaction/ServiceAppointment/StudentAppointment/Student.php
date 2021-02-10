<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment\StudentAppointment;

use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Entity;
use \Arr;

/**
 * Class Student
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment\StudentAppointment
 */
class Student implements Entity
{
    /**
     * @var int
     */
    private int $id;

    /**
     * Student constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)Arr::get($args, 'id', 0);
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
}
