<?php

namespace App\Components\Users\Services\Participant\IEP;

use App\Components\Users\Participant\IEP\IEPDTO;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface IEPServiceContract
 *
 * @package App\Components\Users\Services\Participant\IEP
 */
interface IEPServiceContract
{
    /**
     * @param string $id
     *
     * @return IEPServiceContract
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function workWith(string $id): IEPServiceContract;

    /**
     * @return IEPReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): IEPReadonlyContract;

    /**
     * @return IEPDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): IEPDTO;

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
     * @return IEPServiceContract
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function change(array $data): IEPServiceContract;

    /**
     * @param array $data
     *
     * @return IEPServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function create(array $data): IEPServiceContract;

    /**
     * @return IEPServiceContract
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws BindingResolutionException
     * @throws PermissionDeniedException
     */
    public function remove(): IEPServiceContract;
}
