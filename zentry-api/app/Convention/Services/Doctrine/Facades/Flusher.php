<?php

namespace App\Convention\Services\Doctrine\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Convention\Services\Doctrine\Flusher
 */
class Flusher extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'App\Convention\Services\Doctrine\Flusher';
    }
}