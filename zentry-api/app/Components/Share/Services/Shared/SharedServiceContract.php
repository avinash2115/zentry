<?php

namespace App\Components\Share\Services\Shared;

use App\Components\Share\Contracts\SharableContract;
use App\Components\Share\Shared\SharedDTO;
use App\Components\Share\Shared\SharedReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface SharedServiceContract
 *
 * @package App\Components\Share\Services\Shared
 */
interface SharedServiceContract
{
    /**
     * @param string $id
     *
     * @return SharedServiceContract
     * @throws NotFoundException|BindingResolutionException|UnexpectedValueException|InvalidArgumentException
     */
    public function workWith(string $id): SharedServiceContract;

    /**
     * @param SharableContract $sharable
     *
     * @return SharedServiceContract
     * @throws NotFoundException|BindingResolutionException|UnexpectedValueException|InvalidArgumentException
     */
    public function workWithSharable(SharableContract $sharable): SharedServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return SharedReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): SharedReadonlyContract;

    /**
     * @return SharedDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): SharedDTO;

    /**
     * @param SharableContract $sharable
     *
     * @return SharedServiceContract
     * @throws NotFoundException|BindingResolutionException|PropertyNotInit|RuntimeException
     * @throws InvalidArgumentException
     */
    public function create(SharableContract $sharable): SharedServiceContract;

    /**
     * @return SharedServiceContract
     * @throws PropertyNotInit|NotFoundException|BindingResolutionException
     */
    public function remove(): SharedServiceContract;
}
