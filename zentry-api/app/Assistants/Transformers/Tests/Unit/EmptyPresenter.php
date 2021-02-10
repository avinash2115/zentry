<?php

namespace App\Assistants\Transformers\Tests\Unit;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use Illuminate\Support\Collection;

class EmptyPresenter implements PresenterContract
{
    use PresenterTrait;

    /**
     * @return Collection
     */
    public function attributes(): Collection
    {
        return collect([]);
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return '';
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
