<?php

namespace App\Components\Sessions\Services\Poi\Participant;

use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract as UserParticipantReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface ParticipantServiceContract
 *
 * @package App\Components\Sessions\Services\Poi\Participant
 */
interface ParticipantServiceContract
{
    /**
     * @param string $id
     *
     * @return ParticipantServiceContract
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     */
    public function workWith(string $id): ParticipantServiceContract;

    /**
     * @return ParticipantReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): ParticipantReadonlyContract;

    /**
     * @return Collection
     * @throws BindingResolutionException|InvalidArgumentException|PropertyNotInit
     */
    public function list(): Collection;

    /**
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    public function listRO(): Collection;

    /**
     * @param UserParticipantReadonlyContract $participant
     * @param array                           $data
     *
     * @return ParticipantServiceContract
     * @throws PropertyNotInit
     * @throws Exception
     * @throws RuntimeException
     */
    public function add(UserParticipantReadonlyContract $participant, array $data): ParticipantServiceContract;

    /**
     * @return ParticipantServiceContract
     * @throws NotFoundException
     * @throws PropertyNotInit
     */
    public function remove(): ParticipantServiceContract;
}
