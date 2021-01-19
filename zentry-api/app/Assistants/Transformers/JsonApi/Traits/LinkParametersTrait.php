<?php

namespace App\Assistants\Transformers\JsonApi\Traits;

use App\Assistants\Transformers\JsonApi\LinkParameters;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait LinkParametersTrait
 *
 * @package App\Assistants\Transformers\JsonApi\Traits
 */
trait LinkParametersTrait
{
    /**
     * @var LinkParameters | null
     */
    private ?LinkParameters $linkParameters__ = null;

    /**
     * @return LinkParameters
     * @throws BindingResolutionException
     */
    private function linkParameters__(): LinkParameters
    {
        if (!$this->linkParameters__ instanceof LinkParameters) {
            $this->linkParameters__ = app()->make(LinkParameters::class);
        }

        return $this->linkParameters__;
    }
}
