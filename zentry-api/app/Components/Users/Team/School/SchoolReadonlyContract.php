<?php

namespace App\Components\Users\Team\School;

use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use Illuminate\Support\Collection;

/**
 * Interface SchoolReadonlyContract
 *
 * @package App\Components\Users\Team\School
 */
interface SchoolReadonlyContract extends TimestampableContract, CRMImportableContract
{
    public const DEFAULT_HOME_NAME = 'Home';

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return bool
     */
    public function available(): bool;

    /**
     * @return string|null
     */
    public function streetAddress(): ?string;

    /**
     * @return string|null
     */
    public function city(): ?string;

    /**
     * @return string|null
     */
    public function state(): ?string;

    /**
     * @return string|null
     */
    public function zip(): ?string;

    /**
     * @return Collection
     */
    public function participants(): Collection;
}
