<?php

namespace App\Assistants\Transformers\JsonApi\Traits;

use App\Assistants\Transformers\JsonApi\LinkParameters;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Trait BasicLinksTrait
 *
 * @package App\Assistants\Transformers\JsonApi\Traits
 */
trait BasicLinksTrait
{
    /**
     * @param LinkParameters $linkParameters
     *
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function data(LinkParameters $linkParameters): Collection
    {
        return collect(
            [
                'self' => route(
                    $this->route(),
                    $this->routeParameters()->toArray()
                ),
            ]
        );
    }
}
