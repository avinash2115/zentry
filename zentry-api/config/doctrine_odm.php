<?php

use App\Convention\ValueObjects\Identity\Doctrine\Mongo\IdentityType;

return [
    /* A list of entities */
    'paths' => [
        base_path('app/Components/Sessions/ODMMappings'),
    ],
    'proxies' => [
        'namespace' => 'ODMProxies',
        'path' => storage_path('odm_proxies'),
        'auto_generate' => env('DOCTRINE_PROXY_AUTOGENERATE', false),
    ],
    'hydrators' => [
        'namespace' => 'ODMHydrators',
        'path' => storage_path('odm_hydrators'),
    ],
    'meta' => env('DOCTRINE_METADATA', 'yaml'),
    'connection' => 'mongodb',
    'custom_types' => [
        'identity' => IdentityType::class,
    ],
    'filters' => [],
];
