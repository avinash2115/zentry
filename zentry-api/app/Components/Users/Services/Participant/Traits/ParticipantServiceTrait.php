<?php

namespace App\Components\Users\Services\Participant\Traits;

use App\Components\Users\Services\Participant\ParticipantServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait ParticipantServiceTrait
 *
 * @package App\Components\Users\Services\Participant\Traits
 */
trait ParticipantServiceTrait
{
    /**
     * @var ParticipantServiceContract | null
     */
    private ?ParticipantServiceContract $participantService__ = null;

    /**
     * @return ParticipantServiceContract
     * @throws BindingResolutionException
     */
    private function participantService__(): ParticipantServiceContract
    {
        if (!$this->participantService__ instanceof ParticipantServiceContract) {
            $this->participantService__ = app()->make(ParticipantServiceContract::class);
        }

        return $this->participantService__;
    }
}
