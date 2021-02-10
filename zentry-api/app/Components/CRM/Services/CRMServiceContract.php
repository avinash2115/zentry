<?php

namespace App\Components\CRM\Services;

use App\Assistants\CRM\Exceptions\ConnectionFailed;
use App\Assistants\CRM\Exceptions\InvalidCredentials;
use App\Components\CRM\Contracts\CRMExportableContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface CRMServiceContract
 *
 * @package App\Components\CRM\Services
 */
interface CRMServiceContract
{
    /**
     * @param string $userId
     * @param string $crmId
     *
     * @return CRMServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|PropertyNotInit
     */
    public function workWithUserAndCRM(string $userId, string $crmId): CRMServiceContract;

    /**
     * @return Collection
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
     */
    public function check(): CRMServiceContract;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncTeams(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncServices(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncProviders(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncParticipants(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncParticipantsGoals(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncParticipantsIEPs(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncServiceTransactions(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncProviderTransactions(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncSchools(): void;

    /**
     * @throws NotImplementedException
     * @throws ConnectionFailed
     * @throws InvalidCredentials
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function syncScheduledSessions(): void;

    /**
     * @param CRMExportableContract $entity
     *
     * @throws BindingResolutionException
     */
    public function export(CRMExportableContract $entity): void;
}
