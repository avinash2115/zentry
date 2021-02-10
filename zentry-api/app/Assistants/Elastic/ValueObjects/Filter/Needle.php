<?php

namespace App\Assistants\Elastic\ValueObjects\Filter;

use App\Assistants\Elastic\ValueObjects\Filter\Contracts\NeedlePresenter;
use App\Assistants\Elastic\ValueObjects\Mapping;
use App\Assistants\Elastic\ValueObjects\Mappings;
use Arr;
use Carbon\Carbon;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class Needle
 *
 * @package App\Assistants\Elastic\ValueObjects\Filter
 */
final class Needle implements NeedlePresenter
{
    /**
     * @var string
     */
    private string $attribute;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private bool $allowEmpty;

    /**
     * @param string   $attribute
     * @param mixed    $value
     * @param Mappings $mappings
     * @param bool     $allowEmpty
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function __construct(string $attribute, $value, Mappings $mappings, bool $allowEmpty = false)
    {
        $this->setAttribute($attribute);
        $this->setValue($value, $mappings);
        $this->allowEmpty = $allowEmpty;
    }

    /**
     * @param string $attribute
     *
     * @return Needle
     */
    private function setAttribute(string $attribute): Needle
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return string
     */
    public function attribute(): string
    {
        return $this->attribute;
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
     */
    private function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param mixed    $value
     * @param Mappings $mappings
     *
     * @return $this
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function setValue($value, Mappings $mappings): self
    {
        $this->setType($mappings->mapping($this->attribute())->type());

        switch ($this->type()) {
            case Mapping::NEEDLE_TYPE_IDENTIFIER:
            case Mapping::TYPE_LONG_TEXT:
            case Mapping::TYPE_STRING:
                if (is_array($value)) {
                    collect($value)->each(
                        function ($val) use ($mappings) {
                            $this->validate($val, $mappings);
                        }
                    );
                } else {
                    $this->validate($value, $mappings);
                }
            break;
            case Mapping::TYPE_ARRAY:
                $this->validate($value, $mappings);
            break;
            case Mapping::TYPE_DATE:
                if (!is_array($value) || (!Arr::has($value, 'from') && !Arr::has($value, 'to'))) {
                    throw new InvalidArgumentException(
                        "Type {$this->type()} should contain array with 'from' and\or 'to' keys"
                    );
                }

                if (Arr::has($value, 'from')) {
                    Arr::set($value, 'from', Carbon::parse(Arr::get($value, 'from')));
                    $this->validate(Arr::get($value, 'from'), $mappings);
                }

                if (Arr::has($value, 'to')) {
                    Arr::set($value, 'to', Carbon::parse(Arr::get($value, 'to')));
                    $this->validate(Arr::get($value, 'to'), $mappings);
                }

            break;
            case Mapping::TYPE_NUMBER:
                if (!is_array($value) || (!Arr::has($value, 'from') || !Arr::has($value, 'to'))) {
                    throw new InvalidArgumentException(
                        "Type {$this->type()} should contain array with 'from' and 'to' keys"
                    );
                }

                $this->validate((int)Arr::get($value, 'from'), $mappings);
                $this->validate((int)Arr::get($value, 'to'), $mappings);
            break;
            default:
                throw new InvalidArgumentException('Type is not allowed');
        }

        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        switch ($this->type()) {
            case Mapping::NEEDLE_TYPE_IDENTIFIER:
                $value = $this->value();

                if (!is_array($value)) {
                    $value = [$value];
                }

                return [
                    'terms' => [
                        "_id" => $value,
                    ],
                ];
            case Mapping::TYPE_ARRAY:
                $should = collect($this->value())->map(
                    function ($value) {
                        return [
                            'match' => [
                                "{$this->attribute()}.keyword" => $value,
                            ],
                        ];
                    }
                );

                if ($this->allowEmpty) {
                    $should->push(
                        [
                            "bool" => [
                                "must_not" => [
                                    "exists" => [
                                        "field" => $this->attribute(),
                                    ],
                                ],
                            ],
                        ]
                    );
                } elseif ($should->isEmpty()) {
                    return [
                        "bool" => [
                            "must_not" => [
                                "exists" => [
                                    "field" => $this->attribute(),
                                ],
                            ],
                        ],
                    ];
                }

                return [
                    'bool' => [
                        'should' => $should->values()->toArray(),
                    ],
                ];
            case Mapping::TYPE_LONG_TEXT:
            case Mapping::TYPE_STRING:
                $value = $this->value();

                if (!is_array($value)) {
                    $value = [$value];
                }

                $should = collect($value)->map(
                    function ($value) {
                        return [
                            'match' => [
                                "{$this->attribute()}.keyword" => $value,
                            ],
                        ];
                    }
                );

                if ($this->allowEmpty) {
                    $should->push(
                        [
                            'match' => [
                                "{$this->attribute()}.keyword" => '',
                            ],
                        ]
                    );
                } elseif ($should->isEmpty()) {
                    return [
                        'bool' => [
                            'must_not' => [
                                'exists' => [
                                    'field' => $this->attribute(),
                                ],
                            ],
                        ],
                    ];
                }

                return [
                    'bool' => [
                        'should' => $should->values()->toArray(),
                    ],
                ];
            case Mapping::TYPE_DATE:
                $range = [
                    $this->attribute() => [],
                ];

                if (Arr::has($this->value(), 'from')) {
                    Arr::set(
                        $range,
                        "{$this->attribute()}.gte",
                        dateTimeFormatted(Carbon::instance(Arr::get($this->value(), 'from')))
                    );
                }

                if (Arr::has($this->value(), 'to')) {
                    Arr::set(
                        $range,
                        "{$this->attribute()}.lte",
                        dateTimeFormatted(Carbon::instance(Arr::get($this->value(), 'to')))
                    );
                }

                return [
                    'range' => $range,
                ];
            case Mapping::TYPE_NUMBER:
                return [
                    'range' => [
                        "{$this->attribute()}.numeric" => [
                            'gte' => (int)Arr::get($this->value(), 'from'),
                            'lte' => (int)Arr::get($this->value(), 'to'),
                        ],
                    ],
                ];
            default:
                throw new InvalidArgumentException('Type is not allowed');
        }
    }

    /**
     * @param mixed    $value
     * @param Mappings $mappings
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    private function validate($value, Mappings $mappings): bool
    {
        if (!$mappings->validate($this->attribute(), $value)) {
            $invalidValue = json_encode($value, JSON_THROW_ON_ERROR);

            throw new InvalidArgumentException(
                "The mappings returned false on validation attribute: '{$this->attribute()}', value: {$invalidValue}"
            );
        }

        return true;
    }
}
