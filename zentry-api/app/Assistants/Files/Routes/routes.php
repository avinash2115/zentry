<?php

Route::group(
    [
        'middleware' => [
            'web',
        ],
        'namespace' => 'App\Assistants\Files\Http\Controllers',
    ],
    function () {
        Route::get(
            'download/temporary/{id}',
            ['uses' => 'Controller@downloadViaTemporaryURL', 'as' => 'files.temporary_url.download']
        );
    }
);
