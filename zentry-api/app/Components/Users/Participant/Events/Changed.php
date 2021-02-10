<?php

namespace App\Components\Users\Participant\Events;

use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Queue\SerializesModels;

/**
 * Class Changed
 */
class Changed
{
    /**
     * @var ParticipantReadonlyContract
     */
    private ParticipantReadonlyContract $participant;

    /**
     * @param ParticipantReadonlyContract $participant
     */
    public function __construct(ParticipantReadonlyContract $participant)
    {
        $this->participant = $participant;
    }

    /**
     * @return ParticipantReadonlyContract
     * @throws PropertyNotInit
     */
    public function participant(): ParticipantReadonlyContract
    {
        if (!$this->participant instanceof ParticipantReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->participant;
    }
}
