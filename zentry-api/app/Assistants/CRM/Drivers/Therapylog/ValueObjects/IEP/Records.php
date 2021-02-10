<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\IEP;

use Illuminate\Support\Collection;

/**
 * Class Records
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\IEP
 */
class Records
{
    /**
     * @var Collection
     */
    private Collection $ieps;

    /**
     * @param array $records
     */
    public function __construct(array $records)
    {
        $this->ieps = collect($records)->map(
            static function (array $record) {
                return new IEP($record);
            }
        );
    }

    /**
     * @return Collection
     */
    public function ieps(): Collection
    {
        return $this->ieps;
    }
}
