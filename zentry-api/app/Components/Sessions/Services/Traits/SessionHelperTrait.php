<?php

namespace App\Components\Sessions\Services\Traits;

use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\ValueObjects\Geo;
use App\Convention\ValueObjects\Tags;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;

/**
 * Trait SessionHelperTrait
 *
 * @package App\Components\Sessions\Services\Session\Traits
 */
trait SessionHelperTrait
{
    /**
     * @param array $tags
     *
     * @return Tags
     */
    private function makeTags(array $tags): Tags
    {
        return new Tags($tags);
    }

    /**
     * @param array|null $geo
     *
     * @return Geo|null
     * @throws InvalidArgumentException
     */
    private function makeGeo(?array $geo): ?Geo
    {
        if (!is_array($geo) || !count($geo)) {
            return null;
        }

        return new Geo(Arr::get($geo, 'lng', 0), Arr::get($geo, 'lat', 0), Arr::get($geo, 'place', ''));
    }
}
