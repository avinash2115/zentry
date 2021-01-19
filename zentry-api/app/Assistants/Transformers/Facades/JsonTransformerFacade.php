<?php

namespace App\Assistants\Transformers\Facades;

use Illuminate\Support\Facades\Facade;

class JsonTransformerFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'App\Assistants\Transformers\JsonTransformer';
    }
}