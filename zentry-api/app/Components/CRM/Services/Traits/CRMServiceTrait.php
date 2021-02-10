<?php


namespace App\Components\CRM\Services\Traits;

use App\Components\CRM\Services\CRMServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException as BindingResolutionExceptionAlias;

/**
 * Trait CRMServiceTrait
 *
 * @package App\Components\CRM\Services\Traits
 */
trait CRMServiceTrait
{

    /**
     * @var CRMServiceContract | null
     */
    private ?CRMServiceContract $crmService__ = null;

    /**
     * @return CRMServiceContract
     * @throws BindingResolutionExceptionAlias
     */
    private function crmService__(): CRMServiceContract
    {
        if (!$this->crmService__ instanceof CRMServiceContract) {
            return $this->setCRMService__();
        }

        return $this->crmService__;
    }

    /**
     * @throws BindingResolutionExceptionAlias
     */
    private function setCRMService__(): CRMServiceContract
    {
        $this->crmService__ = app()->make(
            CRMServiceContract::class
        );

        return $this->crmService__;
    }

}
