<?php

namespace App\Components\Users\Services\User\CRM;

use App\Assistants\CRM\Exceptions\ConnectionFailed;
use App\Assistants\CRM\Exceptions\InvalidCredentials;
use App\Components\CRM\Contracts\CRMExportableContract;
use App\Components\Users\User\CRM\CRMDTO;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface CRMServiceContract
 *
 * @package App\Components\Users\Services\User\CRM
 */
interface CRMServiceContract
{
    /**
     * @param string $id
     *
     * @return CRMServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|PropertyNotInit
     */
    public function workWith(string $id): CRMServiceContract;

    /**
     * @param string $driver
     *
     * @return CRMServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|PropertyNotInit
     */
    public function workWithDriver(string $driver): CRMServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return CRMReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): CRMReadonlyContract;

    /**
     * @return CRMDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): CRMDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function listRO(): Collection;

    /**
     * @param array $data
     *
     * @return CRMServiceContract
     * @throws BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException|PropertyNotInit|RuntimeException|ConnectionFailed
     */
    public function connect(array $data): CRMServiceContract;

    /**
     * @return CRMServiceContract
     * @throws PropertyNotInit|BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException|NotFoundException
     */
    public function disconnect(): CRMServiceContract;

    /**
     * @param array $data
     *
     * @return CRMServiceContract
     * @throws PropertyNotInit|BindingResolutionException|NonUniqueResultException|NoResultException|InvalidArgumentException
     */
    public function change(array $data): CRMServiceContract;

    /**
     * @return Collection
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function drivers(): Collection;

    /**
     * @return CRMServiceContract
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws NotFoundException
     */
    public function check(): CRMServiceContract;

    /**
     * @param string|null $type
     *
     * @return CRMServiceContract
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws NotFoundException
     */
    public function sync(?string $type = null): CRMServiceContract;

    /**
     * @param CRMExportableContract $entity
     *
     * @return CRMServiceContract
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws NotFoundException
     */
    public function export(CRMExportableContract $entity): CRMServiceContract;

    /**
     * @param string $type
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    public function lastLog(string $type): Collection;
}
