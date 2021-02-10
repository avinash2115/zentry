<?php

namespace App\Assistants\Transformers\JsonApi\Traits;

use App\Assistants\Transformers\JsonApi\LinkParameters;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Trait NestedLinksTrait
 *
 * @package App\Assistants\Transformers\JsonApi\Traits
 */
trait NestedLinksTrait
{
    /**
     * @param LinkParameters $linkParameters
     *
     * @return Collection
     */
    public function data(LinkParameters $linkParameters): Collection
    {
        return collect(
            [
                'self' => route(
                    $this->route(),
                    $linkParameters->stack()->merge(
                        $this->routeParameters()
                    )->unique()->toArray()
                ),
            ]
        );
    }
}
