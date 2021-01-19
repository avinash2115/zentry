<?php

Route::group(
    [
        'prefix' => '/search',
        'namespace' => 'App\Assistants\Search\Http\Controllers',
        'middleware' => ['web', 'content', 'jwt-auth'],
    ],
    function () {
        Route::get('/', 'Controller@search')->name('search.search');
        Route::get('autocomplete', 'Controller@autocomplete')->name('search.autocomplete');
    }
);
