<?php

namespace App\Assistants\CRM\Drivers\ValueObjects\Converted;

use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Entity;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class ServiceTransaction
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Converted
 */
class ServiceTransaction implements Entity
{
    /**
     * @var Collection
     */
    private Collection $schools;

    /**
     * @var Collection
     */
    private Collection $sessions;

    /**
     * @param Collection $schools
     * @param Collection $sessions
     *
     * @throws Exception
     */
    public function __construct(Collection $schools, Collection $sessions)
    {
        $this->schools = $schools;
        $this->sessions = $sessions;
    }

    /**
     * @return Collection
     */
    public function schools(): Collection
    {
        return $this->schools;
    }

    /**
     * @return Collection
     */
    public function sessions(): Collection
    {
        return $this->sessions;
    }
}
