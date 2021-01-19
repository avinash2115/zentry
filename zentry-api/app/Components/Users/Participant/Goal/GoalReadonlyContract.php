<?php

namespace App\Components\Users\Participant\Goal;

use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Components\Users\ValueObjects\Participant\Goal\Meta;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use Illuminate\Support\Collection;

/**
 * Interface GoalReadonlyContract
 *
 * @package App\Components\Users\Participant\Goal
 */
interface GoalReadonlyContract extends IdentifiableContract, TimestampableContract, CRMImportableContract
{
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function description(): string;

    /**
     * @return bool
     */
    public function isReached(): bool;

    /**
     * @return Meta
     */
    public function meta(): Meta;

    /**
     * @return Collection
     */
    public function trackers(): Collection;

    /**
     * @return IEPReadonlyContract|null
     */
    public function iep(): ?IEPReadonlyContract;
}
