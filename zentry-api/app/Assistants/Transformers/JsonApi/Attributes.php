<?php

namespace App\Assistants\Transformers\JsonApi;

use App\Assistants\Transformers\Contracts\Presenter\AttributesContract;
use App\Assistants\Transformers\JsonApi\Contracts\JsonAPIPresentableContract;
use Illuminate\Support\Collection;

/**
 * Class Attributes
 *
 * @package App\Assistants\Transformers\JsonApi
 */
class Attributes implements JsonAPIPresentableContract
{
    /**
     * @var Collection
     */
    public Collection $attributes;

    /**
     * Attributes constructor.
     *
     * @param AttributesContract $attributes
     * @param Collection         $only
     */
    public function __construct(AttributesContract $attributes, Collection $only)
    {
        $this->setAttributes($attributes, $only);
    }

    /**
     * @return Collection
     */
    public function attributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @return Collection
     */
    public function present(): Collection
    {
        return collect(['attributes' => $this->attributes()->toArray()]);
    }

    /**
     * @param AttributesContract $attributes
     * @param Collection         $only
     *
     * @return Attributes
     */
    private function setAttributes(AttributesContract $attributes, Collection $only): Attributes
    {
        if ($only->isEmpty()) {
            $this->attributes = $attributes->attributes();
        } else {
            $this->attributes = $attributes->attributes()->only(
                $only->toArray()
            );
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->attributes()->isEmpty();
    }
}
