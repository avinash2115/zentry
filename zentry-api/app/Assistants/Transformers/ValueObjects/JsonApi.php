<?php

namespace App\Assistants\Transformers\ValueObjects;

use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class JsonApi
 *
 * @package App\Components\ValueObjects
 */
class JsonApi
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Collection
     */
    private Collection $attributes;

    /**
     * @var Collection
     */
    private Collection $relationships;

    /**
     * @var Collection
     */
    private Collection $raw;

    /**
     * @var Collection
     */
    private Collection $meta;

    /**
     * @param Collection $jsonData
     */
    public function __construct(Collection $jsonData)
    {
        $data = $this->getData($jsonData);
        $this->id = Arr::get($data, 'id', '');
        $this->type = Arr::get($data, 'type', '');
        $this->attributes = collect(Arr::get($data, 'attributes', []));
        $this->relationships = $this->setRelationships(collect(Arr::get($data, 'relationships', [])));
        $this->raw = $jsonData;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
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
    public function relationships(): Collection
    {
        return $this->relationships;
    }

    /**
     * @param string $relationName
     *
     * @return JsonApi | null
     */
    public function relation(string $relationName): ?JsonApi
    {
        $relation = Arr::get($this->relationships, $relationName);

        return $relation instanceof self ? $relation : null;
    }

    /**
     * @param string $relationName
     *
     * @return Collection
     */
    public function relations(string $relationName): Collection
    {
        $relations = Arr::get($this->relationships, $relationName);

        return $relations instanceof Collection ? $relations : collect();
    }

    /**
     * @return Collection
     */
    public function raw(): Collection
    {
        return $this->raw;
    }

    /**
     * @return Collection
     */
    public function asJsonApiCollection(): Collection
    {
        return collect($this->raw()->get('data', []))->mapWithKeys(
            function (array $array) {
                if (Arr::get($array, 'id', '') === '') {
                    Arr::set($array, 'id', IdentityGenerator::next());
                }

                $jsonApi = new JsonApi(collect(['data' => $array]));

                return [$jsonApi->id() => $jsonApi];
            }
        );
    }

    /**
     * @return Collection
     */
    public function meta(): Collection
    {
        return $this->meta;
    }

    /**
     * @param Collection $requestData
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function getData(Collection $requestData): array
    {
        if (!$requestData->has('data')) {
            throw new InvalidArgumentException('Request Should contains data');
        }

        $data = $requestData->get('data');

        if (!is_array($data)) {
            $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            if ($data === null) {
                throw new InvalidArgumentException('data in Request Should be array or json string');
            }
        }

        return $data;
    }

    /**
     * @param Collection $relationships
     *
     * @return Collection
     */
    private function setRelationships(Collection $relationships): Collection
    {
        return $relationships->map(
            function ($value) {
                if (Arr::has($value, 'data.0')) {
                    return collect(Arr::get($value, 'data', []))->map(
                        function ($val) {
                            return new JsonApi(collect(['data' => $val]));
                        }
                    );
                }

                return new JsonApi(collect($value));
            }
        );
    }
}
