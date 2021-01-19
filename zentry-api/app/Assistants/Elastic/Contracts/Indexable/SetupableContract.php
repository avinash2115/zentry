<?php

namespace App\Assistants\Elastic\Contracts\Indexable;

use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mappings;
use App\Assistants\Elastic\ValueObjects\Type;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface SetupableContract
 *
 * @package App\Assistants\Elastic\Contracts\Indexable
 */
interface SetupableContract
{
    /**
     * @return Type
     */
    public function asType(): Type;

    /**
     * @param Index $index
     *
     * @return Mappings
     * @throws IndexNotSupported|BindingResolutionException|RuntimeException|InvalidArgumentException
     */
    public function asMappings(Index $index): Mappings;
}
