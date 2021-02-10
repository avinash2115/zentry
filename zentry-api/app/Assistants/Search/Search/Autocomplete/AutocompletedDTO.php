<?php

namespace App\Assistants\Search\Search\Autocomplete;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use Illuminate\Support\Collection;

/**
 * Class AutocompleteDTO
 *
 * @package App\Assistants\Search\Search\Autocomplete
 */
class AutocompletedDTO implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    public string $_type = 'autocompleted';

    /**
     * @var string
     */
    public string $value;

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect([
            'value' => $this->value,
        ]);
    }
}
