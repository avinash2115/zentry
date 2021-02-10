<?php

namespace App\Components\Users\Services\Participant\Audience;

use App\Components\Users\Participant\ParticipantDTO;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface AudiencableServiceContract
 *
 * @package App\Components\Users\Services\Participant\Audience
 */
interface AudiencableServiceContract
{
    /**
     * @return AudienceServiceContract
     */
    public function audienceService(): AudienceServiceContract;

    /**
     * @param ParticipantDTO $participantDTO
     *
     * @return void
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws NotImplementedException
     */
    public function participantAdded(ParticipantDTO $participantDTO): void;

    /**
     * @param ParticipantDTO $participantDTO
     *
     * @return void
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws NotImplementedException
     */
    public function participantRemoved(ParticipantDTO $participantDTO): void;
}
