<?php

namespace App\Components\Users\Team;

use App\Components\Users\User\UserReadonlyContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use Illuminate\Support\Collection;

/**
 * Interface TeamReadonlyContract
 *
 * @package App\Components\Users\Team
 */
interface TeamReadonlyContract extends TimestampableContract, CRMImportableContract
{
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function description(): ?string;

    /**
     * @return Collection
     */
    public function members(): Collection;

    /**
     * @return UserReadonlyContract
     */
    public function owner(): UserReadonlyContract;

    /**
     * @return Collection
     */
    public function requests(): Collection;

    /**
     * @return Collection
     */
    public function participants(): Collection;

    /**
     * @return Collection
     */
    public function schools(): Collection;

}
