<?php

namespace App\Assistants\CRM\Services;

use App\Assistants\CRM\Drivers\Contracts\CRMExporterInterface;
use App\Assistants\CRM\Drivers\Contracts\CRMImporterInterface;
use App\Assistants\CRM\Drivers\Therapylog\Exporter as TherapyLogExporterAdapter;
use App\Assistants\CRM\Drivers\Therapylog\Importer as TherapyLogImporterAdapter;
use App\Assistants\CRM\Exceptions\UndefinedCRMDriver;
use App\Components\Users\User\CRM\CRMReadonlyContract;

/**
 * Class CRMService
 *
 * @package App\Assistants\CRM\Services
 */
class CRMService implements CRMServiceContract
{
    /**
     * @inheritDoc
     */
    public function importer(string $driver, array $config): CRMImporterInterface
    {
        switch ($driver) {
            case CRMReadonlyContract::DRIVER_THERAPYLOG:
                return app()->make(TherapyLogImporterAdapter::class, ['config' => $config]);

            default:
                throw new UndefinedCRMDriver($driver);
        }
    }

    /**
     * @inheritDoc
     */
    public function exporter(string $driver, array $config): CRMExporterInterface
    {
        switch ($driver) {
            case CRMReadonlyContract::DRIVER_THERAPYLOG:
                return app()->make(TherapyLogExporterAdapter::class, ['config' => $config]);

            default:
                throw new UndefinedCRMDriver($driver);
        }
    }
}
