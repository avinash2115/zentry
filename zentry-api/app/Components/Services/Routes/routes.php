<?php

use App\Components\Services\Service\ServiceDTO;

Route::group(
    [
        'middleware' => [
            'web',
            'content',
            'jwt-auth',
        ],
    ],
    function () {
        Route::prefix('services')->namespace('App\Components\Services\Http\Controllers')->group(
            function () {
                Route::get('/', 'Controller@index')->name('services.index');
                Route::post('/', 'Controller@create')->name('services.create');

                Route::prefix('{serviceId}')->group(
                    function () {
                        Route::get('/', 'Controller@show')->name(ServiceDTO::ROUTE_NAME_SHOW);
                        Route::post('/', 'Controller@change')->name('services.change');
                        Route::delete('/', 'Controller@remove')->name('services.remove');
                    }
                );
            }
        );
    }
);
