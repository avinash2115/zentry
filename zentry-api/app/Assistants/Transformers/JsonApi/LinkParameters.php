<?php

namespace App\Assistants\Transformers\JsonApi;

use Illuminate\Support\Collection;

/**
 * Class LinkParameters
 *
 * @package App\Assistants\Transformers\JsonApi
 */
class LinkParameters
{
    /**
     * @var Collection
     */
    public Collection $stack;

    /**
     * LinkParameters constructor.
     */
    public function __construct()
    {
        $this->stack = collect([]);
    }

    /**
     * @param string $parameter
     *
     * @return LinkParameters
     */
    public function push(string $parameter): LinkParameters
    {
        $exist = clone $this->stack;

        if (!$exist->flip()->has($parameter)) {
            $this->stack->push($parameter);
        }

        return $this;
    }

    /**
     * @param Collection $linkParameters
     *
     * @return LinkParameters
     */
    public function put(Collection $linkParameters): LinkParameters
    {
        $linkParameters->each(
            function ($value, $key) {
                $this->stack->put($key, $value);
            }
        );

        return $this;
    }

    /**
     * @return LinkParameters
     */
    public function pop(): LinkParameters
    {
        $this->stack->pop();

        return $this;
    }

    /**
     * @param string $parameter
     *
     * @return LinkParameters
     */
    public function remove(string $parameter): LinkParameters
    {
        $exist = clone $this->stack->flip();

        if ($exist->has($parameter)) {
            $exist->forget($parameter);
            $this->stack = $exist->flip();
        }

        return $this;
    }

    /**
     * @param string $parameterName
     *
     * @return LinkParameters
     */
    public function removeByName(string $parameterName): LinkParameters
    {
        if ($this->stack->has($parameterName)) {
            $this->stack->forget($parameterName);
        }

        return $this;
    }

    /**
     * @return LinkParameters
     */
    public function flush(): LinkParameters
    {
        $this->stack = collect([]);

        return $this;
    }

    /**
     * @return Collection
     */
    public function stack(): Collection
    {
        return $this->stack;
    }

    /**
     * @param Collection $parameters
     *
     * @return LinkParameters
     */
    public function pushMultiple(Collection $parameters): LinkParameters
    {
        $parameters->map(
            function (string $value) {
                return $this->push($value);
            }
        );

        return $this;
    }
}
