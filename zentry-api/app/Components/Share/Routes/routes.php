<?php

use App\Components\Share\Shared\SharedDTO;

Route::group(
    [
        'middleware' => [
            'web',
            'content',
        ],
    ],
    function () {
        Route::prefix('share')->namespace('App\Components\Share\Http\Controllers\Shared')->group(
            function () {
                Route::prefix('{sharedId}')->group(
                    function () {
                        Route::get('/', 'Controller@show')->name(SharedDTO::ROUTE_NAME_SHOW);
                    }
                );
            }
        );
    }
);
