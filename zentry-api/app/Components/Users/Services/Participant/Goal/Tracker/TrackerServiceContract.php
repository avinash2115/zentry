<?php

namespace App\Components\Users\Services\Participant\Goal\Tracker;

use App\Components\Users\Participant\Goal\Tracker\TrackerDTO;
use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface TrackerServiceContract
 *
 * @package App\Components\Users\Services\Participant\Goal\Tracker
 */
interface TrackerServiceContract
{
    public const DEFAULT_TRACKERS = [
        'Yes' => 'check-circle',
        'No' => 'times-circle',
        'Assist' => 'life-ring',
    ];

    /**
     * @param string $id
     *
     * @return TrackerServiceContract
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function workWith(string $id): TrackerServiceContract;

    /**
     * @return TrackerReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): TrackerReadonlyContract;

    /**
     * @return TrackerDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): TrackerDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws UnexpectedValueException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws UnexpectedValueException
     */
    public function listRO(): Collection;

    /**
     * @param array $data
     *
     * @return TrackerServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function create(array $data): TrackerServiceContract;

    /**
     * @return TrackerServiceContract
     */
    public function createDefault(): TrackerServiceContract;

    /**
     * @param array $data
     *
     * @return TrackerServiceContract
     */
    public function change(array $data): TrackerServiceContract;

    /**
     * @return TrackerServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function remove(): TrackerServiceContract;
}
