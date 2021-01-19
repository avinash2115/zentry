<?php

namespace App\Assistants\Common\Filter\ValueObjects;

use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mapping;
use App\Components\Users\Participant\Mutators\DTO\Mutator as ParticipantMutator;
use App\Components\Users\Services\Participant\ParticipantServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Filter
 *
 * @package App\Assistants\Common\Filter\ValueObjects
 */
final class Filter
{
    use ElasticServiceTrait;

    public const TYPE_SELECT = 'select';

    public const TYPE_ARRAY = 'array';

    public const TYPE_DATEPICKER = 'datepicker';

    public const TYPE_RANGE = 'range';

    public const AVAILABLE_TYPES = [
        self::TYPE_SELECT,
        self::TYPE_DATEPICKER,
        self::TYPE_ARRAY,
    ];

    public const SHOULD_BE_LABELED = [
        ParticipantMutator::TYPE => ParticipantServiceContract::class,
    ];

    /**
     * @var string
     */
    private string $attribute;

    /**
     * @var string
     */
    private string $label;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var int
     */
    private int $weight;

    /**
     * @var Collection
     */
    private Collection $values;

    /**
     * @var Collection
     */
    private Collection $replaceLabels;

    /**
     * @param string          $attribute
     * @param string          $type
     * @param int             $weight
     * @param Collection      $values
     * @param Collection|null $replaceLabels
     * @param string|null     $label
     *
     * @throws BindingResolutionException
     * @throws IndexNotSupported
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __construct(
        string $attribute,
        string $type,
        int $weight,
        Collection $values,
        ?Collection $replaceLabels,
        ?string $label = null
    ) {
        $this->setAttribute($attribute);

        if ($label === null) {
            $label = $attribute;
        }

        $this->setLabel($label);
        $this->setType($type);
        $this->setWeight($weight);
        $this->setValues($values);
        $this->setReplaceLabels($replaceLabels);
        $this->valuesLabels();
    }

    /**
     * @return string
     */
    public function attribute(): string
    {
        return $this->attribute;
    }

    /**
     * @param string $attribute
     */
    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return Filter
     */
    private function setLabel(string $label): Filter
    {
        $this->label = $this->formatLabel($label);

        return $this;
    }

    /**
     * @param string $label
     *
     * @return string
     */
    private function formatLabel(string $label): string
    {
        return Str::title(Str::singular(str_replace("_", " ", $label)));
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    private function setType(string $type): void
    {
        switch ($type) {
            case Mapping::TYPE_STRING:
                $type = self::TYPE_SELECT;
            break;
            case Mapping::TYPE_DATE:
                $type = self::TYPE_DATEPICKER;
            break;
            case Mapping::TYPE_ARRAY:
                $type = self::TYPE_ARRAY;
            break;
            case Mapping::TYPE_NUMBER:
                $type = self::TYPE_RANGE;
            break;
            default:
                throw new InvalidArgumentException("Type {$type} is not allowed");
        }

        $this->type = $type;
    }

    /**
     * @return int
     */
    public function weight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    private function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return Collection
     */
    public function values(): Collection
    {
        return $this->values;
    }

    /**
     * @param Collection $values
     */
    private function setValues(Collection $values): void
    {
        $this->values = $values;
    }

    /**
     * @return Collection
     */
    public function replaceLabels(): Collection
    {
        return $this->replaceLabels;
    }

    /**
     * @param Collection|null $collection
     */
    private function setReplaceLabels(?Collection $collection): void
    {
        $this->replaceLabels = collect();

        if ($collection instanceof Collection) {
            $this->replaceLabels = $collection;
        }
    }

    /**
     * @throws BindingResolutionException
     * @throws IndexNotSupported
     * @throws RuntimeException
     */
    private function valuesLabels(): void
    {
        if (Arr::has(self::SHOULD_BE_LABELED, $this->attribute())) {
            $results = $this->elasticService__()->terms(
                $this->elasticService__()::generateIndex(Index::INDEX_LABELS),
                app()->make(
                    Arr::get(self::SHOULD_BE_LABELED, $this->attribute())
                ),
                $this->values()
            );

            $this->setValues(
                $this->values()->map(
                    function (string $value) use ($results) {
                        $label = $results->first(
                            function (array $result) use ($value) {
                                return (string)Arr::get($result, '_id') === $value;
                            }
                        );

                        return [
                            'label' => is_array($label) ? Arr::get($label, 'label', '') : '',
                            'value' => $value,
                        ];
                    }
                )
            );
        } else {
            $this->setValues(
                $this->values()->map(
                    function (string $value) {
                        $label = $value;

                        if ($this->replaceLabels()->has($this->attribute())) {
                            $valueLabels = $this->replaceLabels()->get($this->attribute());
                            $label = $this->formatLabel(Arr::get($valueLabels, $value, $value));
                        }

                        return [
                            'label' => ucwords(str_replace("_", " ", $label)),
                            'value' => $value,
                        ];
                    }
                )
            );
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'attribute' => $this->attribute(),
            'label' => $this->label(),
            'type' => $this->type(),
            'weight' => $this->weight(),
            'values' => $this->values()->toArray(),
        ];
    }
}
