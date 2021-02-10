<?php

namespace App\Assistants\Transformers\JsonApi;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelatedLinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Contracts\JsonAPIPresentableContract;
use App\Assistants\Transformers\Presenter;
use App\Assistants\Transformers\ValueObjects\JsonApiResponseBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

/**
 * Class Relationships
 *
 * @package App\Assistants\Transformers\JsonApi
 */
class Relationships implements JsonAPIPresentableContract
{
    /**
     * @var Collection
     */
    public Collection $relationships;

    /**
     * @var Collection
     */
    public Collection $includeRules;

    /**
     * @var LinkParameters
     */
    public LinkParameters $linkParameters;

    /**
     * @var JsonApiResponseBuilder | null
     */
    public ?JsonApiResponseBuilder $jsonBuilder = null;

    /**
     * @param LinkParameters    $linkParameters
     * @param PresenterContract $presenter
     * @param Collection|null   $includeRules
     *
     * @throws BindingResolutionException
     */
    public function __construct(
        LinkParameters $linkParameters,
        PresenterContract $presenter,
        ?Collection $includeRules
    ) {
        if (!$presenter instanceof RelationshipsContract) {
            $this->relationships = collect([]);

            return;
        }

        $this->linkParameters = $linkParameters;

        $this->includeRules = $includeRules instanceof Collection ? $includeRules : collect();
        $this->setRelationships($presenter);
    }

    /**
     * @return Collection
     */
    public function present(): Collection
    {
        return collect(
            [
                'relationships' => $this->relationships()->toArray(),
            ]
        );
    }

    /**
     * @return Collection
     */
    private function relationships(): Collection
    {
        return $this->relationships;
    }

    /**
     * @param RelationshipsContract $relationships
     *
     * @return Relationships
     * @throws BindingResolutionException
     */
    private function setRelationships(RelationshipsContract $relationships): Relationships
    {
        if (!$this->_jsonBuilder()->wantsInclude()) {
            $this->relationships = $this->getRelationships($relationships)->map(
                function ($relationship, $name) use ($relationships) {
                    if ($relationship instanceof Collection) {
                        $relationshipArray = [];

                        $bodies = $relationship->values()->map(
                            function ($nestedRelationDTO) {
                                $body = new Body($nestedRelationDTO);

                                return $body->present()->toArray();
                            }
                        )->toArray();

                        $relationshipArray = array_merge($relationshipArray, ['data' => $bodies]);

                        if ($relationships instanceof RelatedLinksContract && $relationships instanceof PresenterContract && $relationships->relatedData(
                                $this->linkParameters
                            )->has($name)) {
                            $links = app()->make(
                                Links::class,
                                [
                                    'presenter' => $relationships,
                                    'related' => true,
                                    'relatedRelationName' => $name,
                                ]
                            );

                            if (!$links->isEmpty()) {
                                $relationshipArray = array_merge($relationshipArray, $links->present()->toArray());
                            }
                        }

                        return $relationshipArray;
                    }

                    if (is_array($relationship)) {
                        return $relationship;
                    }

                    $relationshipArray = [];

                    $body = new Body($relationship);
                    $relationshipArray = array_merge($relationshipArray, ['data' => $body->present()->toArray()]);

                    if ($relationships instanceof LinksContract) {
                        $links = app()->make(
                            Links::class,
                            [
                                'presenter' => $relationship,
                            ]
                        );
                        if (!$links->isEmpty()) {
                            $relationshipArray = array_merge($relationshipArray, $links->present()->toArray());
                        }
                    }

                    return $relationshipArray;
                }
            );
        } else {
            $this->relationships = $this->getRelationships($relationships)->mapWithKeys(
                function ($relationshipDTO, $name) {
                    // should be refactor somehow
                    $presenter = app()->make(Presenter::class);

                    return [$name => ['data' => $presenter->process($relationshipDTO, $this->includeRules)]];
                }
            );
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->relationships()->isEmpty();
    }

    /**
     * @param RelationshipsContract $dto
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    private function getRelationships(RelationshipsContract $dto): Collection
    {
        if (!$this->includeRules instanceof Collection) {
            return collect([]);
        }

        if ($this->_jsonBuilder()->wantsInclude() && !$this->_jsonBuilder()->wantsAllNestedIncludes()) {
            $requiredRelationShips = $this->includeRules->map(
                function ($value, $name) {
                    $this->includeRules->forget($name);
                    if (null !== $value) {
                        $this->includeRules = $this->includeRules->merge(collect($value));
                    }

                    return $name;
                }
            );

            return $dto->nested()->only($requiredRelationShips->keys());
        }

        return $dto->nested()->merge($dto->required());
    }

    /**
     * @return JsonApiResponseBuilder
     * @throws BindingResolutionException
     */
    private function _jsonBuilder(): JsonApiResponseBuilder
    {
        if (!$this->jsonBuilder instanceof JsonApiResponseBuilder) {
            $this->jsonBuilder = app()->make(JsonApiResponseBuilder::class);
        }

        return $this->jsonBuilder;
    }
}
