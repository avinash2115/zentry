<?php

namespace App\Assistants\CRM\Drivers\Contracts;

use App\Assistants\CRM\Drivers\ValueObjects\Converted\ServiceTransaction as ConvertedServiceTransaction;
use App\Assistants\CRM\Drivers\ValueObjects\Converted\ProviderTransaction as ConvertedProviderTransaction;
use App\Assistants\CRM\Exceptions\ConnectionFailed;
use App\Assistants\CRM\Exceptions\InvalidCredentials;
use App\Convention\Exceptions\Logic\NotImplementedException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Interface CRMImporterInterface
 *
 * @package App\Assistants\CRM\Drivers\Contracts
 */
interface CRMImporterInterface
{
    /**
     * Check connection on CRM service
     *
     * @return void
     * @throws BindingResolutionException|InvalidCredentials|ConnectionFailed
     */
    public function check(): void;

    /**
     * @return Collection
     * @throws BindingResolutionException|NotImplementedException
     */
    public function teams(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException|NotImplementedException
     */
    public function services(): Collection;

     /**
     * @return Collection
     * @throws BindingResolutionException|NotImplementedException
     */
    public function providers(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotImplementedException
     * @throws RuntimeException
     */
    public function participants(): Collection;

    /**
     * @return ConvertedServiceTransaction
     * @throws BindingResolutionException
     * @throws NotImplementedException
     * @throws RuntimeException
     * @throws Exception
     */
    public function serviceTransactions(): ConvertedServiceTransaction;

     /**
     * @return ConvertedServiceTransaction
     * @throws BindingResolutionException
     * @throws NotImplementedException
     * @throws RuntimeException
     * @throws Exception
     */
    public function providerTransactions(): ConvertedProviderTransaction;

    /**
     * @param string $participantId
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    public function participantsGoals(string $participantId): Collection;

    /**
     * @param string $participantId
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    public function participantsIEPs(string $participantId): Collection;
}
