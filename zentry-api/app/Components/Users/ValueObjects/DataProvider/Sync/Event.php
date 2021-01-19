<?php

namespace App\Components\Users\ValueObjects\DataProvider\Sync;

use DateTime;
use Illuminate\Support\Collection;

/**
 * Class Event
 *
 * @package App\Components\Users\ValueObjects\DataProvider\Sync
 */
final class Event
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $reference;

    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var string
     */
    private string $scheduledOn;

    /**
     * @var string
     */
    private string $scheduledTo;

    /**
     * @var Collection
     */
    private Collection $participants;

    /**
     * @param string      $name
     * @param string      $reference
     * @param string      $scheduledOn
     * @param string      $scheduledTo
     * @param array       $participants
     * @param string|null $description
     */
    public function __construct(
        string $name,
        string $reference,
        string $scheduledOn,
        string $scheduledTo,
        array $participants,
        string $description = null
    ) {
        $this->name = $name;
        $this->reference = $reference;
        $this->description = $description ?: '';
        $this->scheduledOn = $scheduledOn;
        $this->scheduledTo = $scheduledTo;
        $this->participants = collect($participants);
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
    public function reference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function scheduledOn(): string
    {
        return $this->scheduledOn;
    }

    /**
     * @return string
     */
    public function scheduledTo(): string
    {
        return $this->scheduledTo;
    }

    /**
     * @return Collection
     */
    public function participants(): Collection
    {
        return $this->participants;
    }
}
