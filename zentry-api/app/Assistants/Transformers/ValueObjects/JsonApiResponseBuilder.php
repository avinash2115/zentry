<?php

namespace App\Assistants\Transformers\ValueObjects;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class JsonApiResponseBuilder
 *
 * @package App\Assistants\Transformers\ValueObjects
 */
class JsonApiResponseBuilder
{
    /**
     * @var Collection
     */
    private Collection $includes;

    /**
     * @var Collection
     */
    private Collection $fields;

    /**
     * @var Collection
     */
    private Collection $sortBy;

    /**
     * @var bool
     */
    private bool $allNestedIncludes = false;

    /**
     * @param string $include
     * @param array  $fields
     * @param array  $sortBy
     *
     * @throws UnexpectedValueException
     */
    public function __construct(
        string $include = '',
        array $fields = [],
        array $sortBy = []
    ) {
        $this->setInclude($include);
        $this->setFields($fields);
        $this->setSortBy($sortBy);
    }

    /**
     * @param string|null $include
     *
     * @return bool
     */
    public function wantsInclude(string $include = null): bool
    {
        if ($this->wantsAllNestedIncludes()) {
            return true;
        }

        return $include ? $this->includes->has($include) : !$this->includes->isEmpty();
    }

    /**
     * @return bool
     */
    public function wantsAllNestedIncludes(): bool
    {
        return $this->allNestedIncludes;
    }

    /**
     * @return Collection
     */
    public function includes(): Collection
    {
        return $this->includes;
    }

    /**
     * @param string $include
     *
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function include(string $include): Collection
    {
        if (!$this->wantsInclude($include)) {
            throw new InvalidArgumentException('No includes founded for type: ' . $include);
        }

        return collect($this->includes->get($include));
    }

    /**
     * @param string $include
     *
     * @return JsonApiResponseBuilder
     */
    private function setInclude(string $include): JsonApiResponseBuilder
    {
        if ($include === '*') {
            $this->allNestedIncludes = true;
            $this->includes = collect([]);

            return $this;
        }

        $includes = array_filter(explode(',', $include));
        $newArray = [];

        foreach ($includes as $includeEntity) {
            Arr::set($newArray, $includeEntity, null);
        }

        $this->includes = collect($newArray);

        return $this;
    }

    /**
     * @param string|null $type
     *
     * @return bool
     */
    public function wantsSpecifiedFields(string $type = null): bool
    {
        return $type ? $this->fields->has($type) : !$this->fields->isEmpty();
    }

    /**
     * @param string|null $type
     *
     * @return Collection
     * @throws InvalidArgumentException
     */
    public function fields(string $type = null): Collection
    {
        $exist = $this->wantsSpecifiedFields($type);

        if (!$exist) {
            throw new InvalidArgumentException('No fields founded for type: ' . $type);
        }

        return collect($this->fields->get($type));
    }

    /**
     * @param array $fields
     *
     * @return JsonApiResponseBuilder
     * @throws UnexpectedValueException
     */
    private function setFields(array $fields): JsonApiResponseBuilder
    {
        $this->fields = collect([]);
        foreach ($fields as $relationName => $value) {
            if (!is_string($value)) {
                throw new UnexpectedValueException('Wrong fields data');
            }

            $this->fields->put($relationName, explode(',', $value));
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function sortBy(): Collection
    {
        return $this->sortBy;
    }

    /**
     * @param array $sortBy
     *
     * @return JsonApiResponseBuilder
     */
    private function setSortBy(array $sortBy): JsonApiResponseBuilder
    {
        $this->sortBy = collect($sortBy);

        return $this;
    }
}
