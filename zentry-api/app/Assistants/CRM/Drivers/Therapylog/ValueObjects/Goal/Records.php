<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Goal;

use Illuminate\Support\Collection;

/**
 * Class Records
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Goal
 */
class Records
{
    /**
     * @var Collection
     */
    private Collection $goals;

    /**
     * @param array $records
     */
    public function __construct(array $records)
    {
        $this->goals = collect($records)->map(
            static function (array $record) {
                return new Goal($record);
            }
        );
    }

    /**
     * @return Collection
     */
    public function goals(): Collection
    {
        return $this->goals;
    }
}
