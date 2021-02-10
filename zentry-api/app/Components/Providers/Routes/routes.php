<?php

use App\Components\Providers\ProviderService\ProviderDTO;

Route::group(
    [
        'middleware' => [
            'web',
            'content',
            'jwt-auth',
        ],
    ],
    function () {
        Route::prefix('providers')->namespace('App\Components\Providers\Http\Controllers')->group(
            function () {
                Route::get('/', 'Controller@index')->name('providers.index');
                Route::post('/', 'Controller@create')->name('providers.create');

                Route::prefix('{providerId}')->group(
                    function () {
                        Route::get('/', 'Controller@show')->name(ProviderDTO::ROUTE_NAME_SHOW);
                        Route::post('/', 'Controller@change')->name('providers.change');
                        Route::delete('/', 'Controller@remove')->name('providers.remove');
                    }
                );
            }
        );
    }
);
