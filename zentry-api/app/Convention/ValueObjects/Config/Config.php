<?php

namespace App\Convention\ValueObjects\Config;

use App\Convention\Contracts\Arrayable;
use Arr;
use Crypt;
use Illuminate\Support\Collection;

/**
 * Class Config
 *
 * @package App\Convention\ValueObjects\Config
 */
class Config implements Arrayable
{
    /**
     * @var Collection
     */
    private Collection $options;

    /**
     * @param array $options
     * @param bool  $decryptBeforeEncrypt
     */
    public function __construct(array $options, bool $decryptBeforeEncrypt = false)
    {
        $this->setOptions($options, $decryptBeforeEncrypt);
    }

    /**
     * @return Collection
     */
    public function options(): Collection
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @param bool  $decryptBeforeEncrypt
     *
     * @return Config
     */
    public function setOptions(array $options, bool $decryptBeforeEncrypt = false): Config
    {
        $this->options = collect($options)->map(function (array $option) use ($decryptBeforeEncrypt) {
            $value = Arr::get($option, 'value', '');
            $encryption = filter_var(Arr::get($option, 'encryption', false), FILTER_VALIDATE_BOOLEAN);

            if ($encryption && $decryptBeforeEncrypt) {
                $value = Crypt::decryptString($value);
            }

            return new Option(Arr::get($option, 'type', ''), Arr::get($option, 'title', Arr::get($option, 'type', '')), $value, $encryption, filter_var(Arr::get($option, 'hidden', false), FILTER_VALIDATE_BOOLEAN));
        });

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->options()->map(function (Option $option) {
            return $option->toArray();
        })->toArray();
    }

    /**
     * @return array
     */
    public function toArrayDecrypted(): array
    {
        return $this->options()->map(function (Option $option) {
            return $option->toArrayDecrypted();
        })->toArray();
    }

    /**
     * @return Collection
     */
    public function asTypeValueMap(): Collection
    {
        return $this->options()->mapWithKeys(function (Option $option) {
            return [$option->type() => $option->encryption() ? Crypt::decryptString($option->value()) : $option->value()];
        });
    }
}
