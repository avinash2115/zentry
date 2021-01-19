<?php

namespace App\Assistants\Transformers\Traits;

use App\Assistants\Transformers\Presenter;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait PresenterTrait
 *
 * @package App\Assistants\Transformers\Traits
 */
trait PresenterTrait
{
    /**
     * @var Presenter | null
     */
    private ?Presenter $presenter__ = null;

    /**
     * @return Presenter
     * @throws BindingResolutionException
     */
    private function presenter__(): Presenter
    {
        if (!$this->presenter__ instanceof Presenter) {
            $this->presenter__ = app()->make(Presenter::class);
        }

        return $this->presenter__;
    }
}
