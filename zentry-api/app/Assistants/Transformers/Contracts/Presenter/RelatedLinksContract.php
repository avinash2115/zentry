<?php

namespace App\Assistants\Transformers\Contracts\Presenter;

use App\Assistants\Transformers\JsonApi\LinkParameters;
use Illuminate\Support\Collection;

/**
 * Interface RelatedLinksContract
 *
 * @package App\Assistants\Transformers\Contracts\Presenter
 */
interface RelatedLinksContract
{
    /**
     * @param LinkParameters $linkParameters
     *
     * @return Collection
     */
    public function relatedData(LinkParameters $linkParameters): Collection;
}
