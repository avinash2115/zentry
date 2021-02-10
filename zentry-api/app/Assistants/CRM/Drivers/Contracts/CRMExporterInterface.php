<?php

namespace App\Assistants\CRM\Drivers\Contracts;

use App\Assistants\CRM\Drivers\DTO\Session\SessionDTO;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface CRMExporterInterface
 *
 * @package App\Assistants\CRM\Drivers\Contracts
 */
interface CRMExporterInterface
{
    /**
     * @param SessionDTO $dto
     *
     * @return string
     * @throws BindingResolutionException
     */
    public function createSession(SessionDTO $dto): string;

    /**
     * @param SessionDTO $dto
     *
     * @return string
     * @throws BindingResolutionException
     */
    public function updateSession(SessionDTO $dto): string;
}
