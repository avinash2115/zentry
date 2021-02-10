<?php

namespace App\Assistants\Transformers\Contracts\Presenter;

use Illuminate\Support\Collection;

/**
 * Interface AttributesContract
 *
 * @package App\Assistants\Transformers\Contracts\Presenter
 */
interface AttributesContract
{
    /**
     * @return Collection
     */
    public function attributes(): Collection;
}
