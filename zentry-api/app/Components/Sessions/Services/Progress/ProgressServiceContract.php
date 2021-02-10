<?php

namespace App\Components\Sessions\Services\Progress;

use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Progress\ProgressDTO;
use App\Components\Sessions\Session\Progress\ProgressReadonlyContract;
use App\Components\Sessions\ValueObjects\Progress\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface ProgressServiceContract
 *
 * @package App\Components\Sessions\Services\Progress
 */
interface ProgressServiceContract
{
    /**
     * @param string $id
     *
     * @return ProgressServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|NotFoundException|UnexpectedValueException
     */
    public function workWith(string $id): ProgressServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return ProgressReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): ProgressReadonlyContract;

    /**
     * @return ProgressDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): ProgressDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function list(): Collection;

    /**
     * @return Collection
     */
    public function listRO(): Collection;

    /**
     * @param Payload                  $payload
     * @param PoiReadonlyContract|null $poi
     *
     * @return ProgressServiceContract
     * @throws BindingResolutionException
     */
    public function create(Payload $payload, PoiReadonlyContract $poi = null): ProgressServiceContract;

    /**
     * @return ProgressServiceContract
     * @throws BindingResolutionException|PropertyNotInit|NotFoundException
     */
    public function remove(): ProgressServiceContract;
}
