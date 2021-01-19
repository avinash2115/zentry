<?php

namespace App\Components\Users\Services\Participant\Audience;

use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface AudienceServiceContract
 *
 * @package App\Components\Users\Services\Participant\Audience
 */
interface AudienceServiceContract
{
    /**
     * @param ParticipantReadonlyContract $participant
     *
     * @return AudienceServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function add(ParticipantReadonlyContract $participant): AudienceServiceContract;

    /**
     * @param ParticipantReadonlyContract $participant
     *
     * @return AudienceServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function kick(ParticipantReadonlyContract $participant): AudienceServiceContract;

    /**
     * @throws BindingResolutionException
     */
    public function listRO(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function list(): Collection;
}
