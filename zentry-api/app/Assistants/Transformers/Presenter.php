<?php

namespace App\Assistants\Transformers;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Attributes;
use App\Assistants\Transformers\JsonApi\Body;
use App\Assistants\Transformers\JsonApi\Contracts\JsonAPIPresentableContract;
use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Assistants\Transformers\JsonApi\Links;
use App\Assistants\Transformers\JsonApi\Relationships;
use App\Assistants\Transformers\ValueObjects\JsonApiResponseBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class Presenter
 *
 * @package App\Assistants\Transformers
 */
class Presenter
{
    /**
     * @var JsonApiResponseBuilder|null
     */
    public ?JsonApiResponseBuilder $responseBuilder = null;

    /**
     * @var LinkParameters|null
     */
    public ?LinkParameters $linkParameters = null;

    /**
     * @param mixed $dto
     * @param array $meta
     *
     * @return array
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function present($dto, array $meta = []): array
    {
        if (!$dto instanceof Collection && !$dto instanceof PresenterContract) {
            throw new InvalidArgumentException('Wrong dto instance. Only Collection or PresenterContract allow.');
        }

        $data = [
            'data' => $this->process($dto, clone $this->_responseBuilder()->includes()),
        ];

        if (count($meta) > 0) {
            Arr::set($data, 'meta', $meta);
        }

        return $data;
    }

    /**
     * @param PresenterContract|Collection $dto
     * @param Collection                   $includeRules
     *
     * @return array
     * @throws InvalidArgumentException|BindingResolutionException
     */
    public function process($dto, Collection $includeRules): array
    {
        if ($dto instanceof Collection) {
            return $this->processCollection($dto, $includeRules);
        }

        if ($dto instanceof LinksContract) {
            $this->_linkParameters()->put($dto->routeParameters());
        }

        $result = $this->processSingle($dto, $includeRules);

        if ($dto instanceof LinksContract) {
            $dto->routeParameters()->each(
                function ($value, $name) {
                    $this->_linkParameters()->removeByName($name);
                }
            );
        } else {
            $this->_linkParameters()->remove($dto->id());
        }

        return $result;
    }

    /**
     * @param PresenterContract $dto
     * @param Collection        $includeRules
     *
     * @return array
     * @throws InvalidArgumentException|BindingResolutionException
     */
    private function processSingle(PresenterContract $dto, Collection $includeRules): array
    {
        $body = new Body($dto);

        $specifiedFields = $this->_responseBuilder()->wantsSpecifiedFields($dto->type()) ? $this->_responseBuilder()
            ->fields($dto->type()) : collect([]);

        $attributes = new Attributes($dto, $specifiedFields);

        $links = app()->make(
            Links::class,
            [
                'presenter' => $dto,
            ]
        );

        if (!$dto instanceof RelationshipsContract) {
            return $this->glue($body, $attributes, $links);
        }

        $relations = app()->make(
            Relationships::class,
            [
                'presenter' => $dto,
                'includeRules' => clone $includeRules,
            ]
        );

        return $this->glue($body, $attributes, $links, $relations);
    }

    /**
     * @param Collection $dtos
     * @param Collection $includeRules
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function processCollection(Collection $dtos, Collection $includeRules): array
    {
        return $dtos->values()->map(
            function (PresenterContract $item) use ($includeRules) {
                return $this->process($item, $includeRules);
            }
        )->toArray();
    }

    /**
     * @param JsonAPIPresentableContract ...$args
     *
     * @return array
     */
    private function glue(JsonAPIPresentableContract ...$args): array
    {
        $jsonAPIParts = collect(\func_get_args());

        $filtered = collect(
            $jsonAPIParts->filter(
                function (JsonAPIPresentableContract $part) {
                    return !$part->isEmpty();
                }
            )
        );

        return $filtered->mapWithKeys(
            function (JsonAPIPresentableContract $part) {
                return $part->present()->toArray();
            }
        )->toArray();
    }

    /**
     * @return JsonApiResponseBuilder
     * @throws BindingResolutionException
     */
    private function _responseBuilder(): JsonApiResponseBuilder
    {
        if (!$this->responseBuilder instanceof JsonApiResponseBuilder) {
            $this->responseBuilder = app()->make(JsonApiResponseBuilder::class);
        }

        return $this->responseBuilder;
    }

    /**
     * @return LinkParameters
     * @throws BindingResolutionException
     */
    private function _linkParameters(): LinkParameters
    {
        if (!$this->linkParameters instanceof LinkParameters) {
            $this->linkParameters = app()->make(LinkParameters::class);
        }

        return $this->linkParameters;
    }
}
