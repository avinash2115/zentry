<?php


namespace App\Assistants\CRM\Services;

use App\Assistants\CRM\Drivers\Contracts\CRMExporterInterface;
use App\Assistants\CRM\Drivers\Contracts\CRMImporterInterface;
use App\Assistants\CRM\Exceptions\UndefinedCRMDriver;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface CRMServiceContract
 *
 * @package App\Assistants\CRM\Services
 */
interface CRMServiceContract
{
    /**
     * @param string $driver
     * @param array $config
     *
     * @return CRMImporterInterface
     * @throws BindingResolutionException|UndefinedCRMDriver
     */
    public function importer(string $driver, array $config): CRMImporterInterface;

    /**
     * @param string $driver
     * @param array $config
     *
     * @return CRMExporterInterface
     * @throws BindingResolutionException|UndefinedCRMDriver
     */
    public function exporter(string $driver, array $config): CRMExporterInterface;

}
