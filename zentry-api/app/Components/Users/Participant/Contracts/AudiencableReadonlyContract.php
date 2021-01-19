<?php

namespace App\Components\Users\Participant\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface ParticipantableReadonlyContract
 *
 * @package App\Components\Users\Contracts\Participant
 */
interface AudiencableReadonlyContract
{
    /**
     * @return Collection
     */
    public function participants(): Collection;
}
