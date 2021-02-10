<?php

namespace App\Components\CRM\Services\Source\Traits;

use App\Components\CRM\Services\Source\SourceServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait SourceServiceTrait
 *
 * @package App\Components\CRM\Services\Source\Traits
 */
trait SourceServiceTrait
{
    /**
     * @var SourceServiceContract | null
     */
    private ?SourceServiceContract $sourceService__ = null;

    /**
     * @return SourceServiceContract
     * @throws BindingResolutionException
     */
    private function sourceService__(): SourceServiceContract
    {
        if (!$this->sourceService__ instanceof SourceServiceContract) {
            $this->sourceService__ = app()->make(SourceServiceContract::class);
        }

        return $this->sourceService__;
    }
}
