<?php

namespace App\Assistants\Transformers\Contracts\Presenter;

use Illuminate\Support\Collection;

/**
 * Interface RelationshipsContract
 *
 * @package App\Assistants\Transformers\Contracts\Presenter
 */
interface RelationshipsContract
{
    /**
     * @return Collection
     */
    public function nested(): Collection;

    /**
     * @return Collection
     */
    public function required(): Collection;
}
