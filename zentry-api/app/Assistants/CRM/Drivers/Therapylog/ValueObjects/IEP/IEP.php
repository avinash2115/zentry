<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\IEP;

use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Goal\Records as GoalsRecords;
use Arr;
use InvalidArgumentException;

/**
 * Class IEP
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\IEP
 */
class IEP
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $effectiveOn;

    /**
     * @var string
     */
    private string $reevalDate;

    /**
     * @var GoalsRecords
     */
    private GoalsRecords $goals;

    /**
     * @var array
     */
    private array $raw;

    /**
     * Caseload constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (strEmpty(Arr::get($args, 'id', ''))) {
            throw new InvalidArgumentException('ID must be present');
        }

        $this->id = Arr::get($args, 'id', '');
        $this->effectiveOn = Arr::get($args, 'effective_on', '');
        $this->reevalDate = Arr::get($args, 'reeval_date', '');
        $this->goals = new GoalsRecords(Arr::get($args, 'goals', []));
        $this->raw = $args;
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
    public function effectiveOn(): string
    {
        return $this->effectiveOn;
    }

    /**
     * @return string
     */
    public function reevalDate(): string
    {
        return $this->reevalDate;
    }

    /**
     * @return GoalsRecords
     */
    public function goals(): GoalsRecords
    {
        return $this->goals;
    }

    /**
     * @return array
     */
    public function raw(): array
    {
        return $this->raw;
    }
}
