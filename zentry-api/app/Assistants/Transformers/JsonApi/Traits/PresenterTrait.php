<?php

namespace App\Assistants\Transformers\JsonApi\Traits;

use App\Assistants\Transformers\Contracts\Presenter\AttributesContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use UnexpectedValueException;

/**
 * Trait PresenterTrait
 *
 * @package App\Assistants\Transformers\JsonApi\Traits
 */
trait PresenterTrait
{
    use IdTrait;

    /**
     * @var array
     */
    public array $meta = [];

    /**
     * @var bool
     */
    public bool $linksEnabled = true;

    /**
     * @return string
     * @throws UnexpectedValueException
     */
    public function route(): string
    {
        if (!isset($this->route) || strEmpty($this->route)) {
            throw new UnexpectedValueException('Route must contains value.');
        }

        return $this->route;
    }

    /**
     * @return string
     * @throws UnexpectedValueException
     */
    public function type(): string
    {
        if (!isset($this->_type) || strEmpty($this->_type)) {
            throw new UnexpectedValueException('Type must contains value.');
        }

        return $this->_type;
    }

    /**
     * @return array
     */
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * @return array
     * @throws UnexpectedValueException
     */
    public function present(): array
    {
        $result = [
            'type' => $this->type(),
            'id' => $this->id(),
        ];

        if ($this->meta()) {
            $result['meta'] = $this->meta();
        }

        return $result;
    }

    /**
     * @return Collection
     */
    public function routeParameters(): Collection
    {
        if (!isset($this->routeParameterName) || strEmpty($this->routeParameterName)) {
            throw new UnexpectedValueException('routeParameterName must exist.');
        }

        return collect(
            [
                $this->routeParameterName => $this->id(),
            ]
        );
    }

    /**
     * @return bool
     */
    public function linksEnabled(): bool
    {
        return $this->linksEnabled;
    }

    /**
     * @return void
     */
    public function enableLinks(): void
    {
        $this->linksEnabled = true;
    }

    /**
     * @return void
     */
    public function disableLinks(): void
    {
        $this->linksEnabled = false;
    }

    /**
     * @var array $meta
     */
    public function fillMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        if (!$this instanceof AttributesContract) {
            throw new NotImplementedException(__METHOD__, __CLASS__);
        }

        $result = $this->attributes();

        if ($this instanceof RelationshipsContract) {
            $result->merge($this->nested());
        }

        return $result->mapWithKeys(fn($value, string $attribute) => [Str::camel($attribute) => $value])->toArray();
    }
}
