<?php

namespace App\Assistants\Transformers\Contracts\Presenter;

use App\Assistants\Transformers\JsonApi\LinkParameters;
use Illuminate\Support\Collection;
use UnexpectedValueException;

/**
 * Interface LinksContract
 *
 * @package App\Assistants\Transformers\Contracts\Presenter
 */
interface LinksContract
{
    /**
     * @param LinkParameters $linkParameters
     *
     * @return Collection
     * @throws UnexpectedValueException
     */
    public function data(LinkParameters $linkParameters): Collection;

    /**
     * @return Collection
     */
    public function routeParameters(): Collection;
}
