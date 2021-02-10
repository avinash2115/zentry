<?php

use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Session\Note\NoteDTO;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\Progress\ProgressDTO;
use App\Components\Sessions\Session\SessionDTO;
use App\Components\Sessions\Session\SOAP\SOAPDTO;
use App\Components\Sessions\Session\Stream\StreamDTO;
use App\Http\Middleware\Access\Device\Authenticate as AuthenticateDevice;
use App\Http\Middleware\Access\Shared\Authenticate;

Route::group(
    [
        'middleware' => [
            'web',
            'content',
            'jwt-auth',
        ],
    ],
    function () {
        Route::prefix('sessions')->namespace('App\Components\Sessions\Http\Controllers')->group(
            function () {
                Route::get('/', 'Controller@index')->name('sessions.index')->middleware(AuthenticateDevice::ALIAS);

                Route::get('active', 'Controller@active')->name('sessions.active')->middleware(
                    AuthenticateDevice::ALIAS
                );

                Route::get('scheduled', 'Controller@scheduled')->name('sessions.scheduled');

                Route::get('dead', 'Controller@dead')->name('sessions.dead')->middleware(AuthenticateDevice::ALIAS);
                Route::post('start', 'Controller@adhoc')->name('sessions.adhoc');
                Route::post('schedule', 'Controller@schedule')->name('sessions.schedule');

                Route::prefix('{sessionId}')->middleware(Authenticate::ALIAS)->group(
                    function () {
                        Route::get('/', 'Controller@show')->name(SessionDTO::ROUTE_NAME_SHOW);
                        Route::post('/', 'Controller@change')->name('sessions.change')->middleware(
                            AuthenticateDevice::ALIAS
                        )->withoutMiddleware(
                            Authenticate::ALIAS
                        );
                        Route::delete('/', 'Controller@remove')->name('sessions.remove')->withoutMiddleware(
                            Authenticate::ALIAS
                        );

                        Route::post('start', 'Controller@start')->name('sessions.start');
                        Route::get('qr', 'Controller@qr')->name('sessions.qr')->withoutMiddleware(
                            'content'
                        );
                        Route::post('end', 'Controller@end')->name('sessions.end')->middleware(
                            AuthenticateDevice::ALIAS
                        );
                        Route::post('wrap', 'Controller@wrap')->name('sessions.wrap');

                        Route::post('/post_process', 'Controller@postProcess')->name(
                            'sessions.post_process'
                        )->withoutMiddleware(
                            Authenticate::ALIAS
                        );
                        Route::post('share', 'Controller@share')->name('sessions.share');
                        Route::delete('unshare', 'Controller@unshare')->name('sessions.unshare');

                        Route::prefix('{poiId}/transcription')->namespace('Transcription')->group(
                            function () {
                                Route::post('/', 'Controller@create')->name(
                                    'sessions.transcription.create'
                                )->withoutMiddleware('jwt-auth');
                                Route::post('failed', 'Controller@failed')->name(
                                    'sessions.transcription.failed'
                                )->withoutMiddleware('jwt-auth');
                            }
                        );

                        Route::prefix('relationships/streams')->namespace('Stream')->group(
                            function () {
                                Route::get('/', 'Controller@index')->name(
                                    'sessions.streams.index'
                                );

                                Route::put('upload/{streamType}', 'Controller@upload')->name(
                                    'sessions.streams.upload'
                                )->withoutMiddleware('content');

                                Route::prefix('partial')->group(
                                    function () {
                                        Route::get('upload/{streamType}', 'Controller@isPartUploaded')->name(
                                            'sessions.streams.partial.uploaded'
                                        )->withoutMiddleware('content');

                                        Route::put('upload/{streamType}', 'Controller@partialReceive')->name(
                                            'sessions.streams.partial.receive'
                                        )->withoutMiddleware('content');

                                        Route::post('merge/{streamType}', 'Controller@partialMerge')->name(
                                            'sessions.streams.partial.merge'
                                        );
                                    }
                                );

                                Route::prefix('{streamId}')->group(
                                    function () {
                                        Route::get('/', function () { })->name(
                                            StreamDTO::ROUTE_NAME_SHOW
                                        );

                                        // CRITICAL solve the access from microservices

                                        Route::get('temporary_url', 'Controller@temporaryUrl')->name(
                                            'sessions.streams.temporary_url'
                                        )->withoutMiddleware('jwt-auth');

                                        Route::get('token', 'Controller@token')->name(
                                            'sessions.streams.token'
                                        );

                                        Route::get('token/{tokenId}', 'Controller@play')->name(
                                            'sessions.streams.play'
                                        )->withoutMiddleware(
                                            ['content', App\Http\Middleware\Access\JWT\Authenticate::ALIAS]
                                        );
                                    }
                                );
                            }
                        );

                        Route::prefix('device')->namespace('Device')->group(
                            function () {
                                Route::post('connect', 'Controller@connect')->name(
                                    SessionServiceContract::ROUTE_CONNECT_DEVICE
                                )->withoutMiddleware('jwt-auth');

                                Route::middleware(AuthenticateDevice::ALIAS)->group(
                                    function () {
                                        Route::delete('disconnect', 'Controller@disconnect')->name(
                                            'sessions.devices.disconnect'
                                        )->withoutMiddleware('jwt-auth');

                                        Route::post('save', 'Controller@save')
                                            ->name('sessions.devices.save')
                                            ->withoutMiddleware('jwt-auth');
                                    }
                                );
                            }
                        );

                        Route::prefix('relationships/notes')
                            ->middleware(AuthenticateDevice::ALIAS)
                            ->namespace('Note')
                            ->group(
                                function () {
                                    Route::get('/', 'Controller@index')->name('sessions.notes.index');
                                    Route::post('/', 'Controller@create')->name('sessions.notes.create');
                                    Route::put('/', 'Controller@upload')
                                        ->name('sessions.notes.upload')
                                        ->withoutMiddleware('content');

                                    Route::prefix('{noteId}')->group(
                                        function () {
                                            Route::get('/', 'Controller@show')->name(NoteDTO::ROUTE_NAME_SHOW);
                                            Route::post('/', 'Controller@change')->name('sessions.notes.change');
                                            Route::put('/', 'Controller@reupload')
                                                ->name('sessions.notes.reupload')
                                                ->withoutMiddleware('content');
                                            Route::delete('/', 'Controller@remove')->name('sessions.notes.remove');
                                        }
                                    );
                                }
                            );

                        Route::prefix('relationships/soaps')
                            ->middleware(AuthenticateDevice::ALIAS)
                            ->namespace('SOAP')
                            ->group(
                                function () {
                                    Route::get('/', 'Controller@index')->name('sessions.soaps.index');
                                    Route::post('/', 'Controller@create')->name('sessions.soaps.create');
                                    Route::post('/bulk', 'Controller@createBulk')->name('sessions.soaps.create_bulk');

                                    Route::prefix('{soapId}')->group(
                                        function () {
                                            Route::get('/', 'Controller@show')->name(SOAPDTO::ROUTE_NAME_SHOW);
                                            Route::post('/', 'Controller@change')->name('sessions.soaps.change');
                                            Route::delete('/', 'Controller@remove')->name('sessions.soaps.remove');
                                        }
                                    );
                                }
                            );

                        Route::prefix('relationships/pois')
                            ->middleware(AuthenticateDevice::ALIAS)
                            ->namespace('Poi')
                            ->group(
                                function () {
                                    Route::get('/', 'Controller@index')->name('sessions.pois.index');
                                    Route::post('/', 'Controller@create')->name('sessions.pois.create');

                                    Route::prefix('transcripts')->group(
                                        function () {
                                            Route::get('/', 'Controller@transcript')->name('sessions.pois.transcripts');
                                        }
                                    );

                                    Route::prefix('{poiId}')->group(
                                        function () {
                                            Route::get('/', 'Controller@show')->name(PoiDTO::ROUTE_NAME_SHOW);
                                            Route::post('/', 'Controller@change')->name('sessions.pois.change');
                                            Route::delete('/', 'Controller@remove')->name('sessions.pois.remove');

                                            Route::get('temporary_url', 'Controller@temporaryUrl')->name(
                                                'sessions.pois.temporary_url'
                                            );

                                            Route::get('token', 'Controller@token')->name(
                                                'sessions.pois.token'
                                            );

                                            Route::get('token/{tokenId}', 'Controller@play')->name(
                                                'sessions.pois.play'
                                            )->withoutMiddleware(
                                                ['content', App\Http\Middleware\Access\JWT\Authenticate::ALIAS, AuthenticateDevice::ALIAS]
                                            );

                                            Route::post('share', 'Controller@share')->name(
                                                'sessions.pois.share'
                                            );
                                            Route::delete('unshare', 'Controller@unshare')->name(
                                                'sessions.pois.unshare'
                                            );

                                            Route::prefix('relationships/transcripts')->group(
                                                function () {
                                                    Route::get('/', 'Controller@transcript')->name(
                                                        'sessions.poi.transcript'
                                                    );
                                                }
                                            );

                                            Route::prefix('relationships/participants')->group(
                                                function () {
                                                    Route::post('/', 'Controller@addParticipants')->name(
                                                        'sessions.participants.add'
                                                    );

                                                    Route::delete('/', 'Controller@removeParticipants')->name(
                                                        'sessions.poi.participants.remove'
                                                    )->middleware(
                                                        AuthenticateDevice::ALIAS
                                                    );
                                                }
                                            );
                                        }
                                    );
                                }
                            );

                        Route::prefix('relationships/participants')->group(
                            function () {
                                Route::post('/', 'Controller@addParticipants')->name(
                                    'sessions.participants.add'
                                )->middleware(
                                    AuthenticateDevice::ALIAS
                                );

                                Route::delete('/', 'Controller@removeParticipants')->name(
                                    'sessions.participants.remove'
                                )->middleware(
                                    AuthenticateDevice::ALIAS
                                );
                            }
                        );

                        Route::prefix('relationships/progress')->middleware(AuthenticateDevice::ALIAS)->namespace(
                            'Progress'
                        )->group(
                            function () {
                                Route::get('/', 'Controller@index')->name('sessions.progress.index');
                                Route::post('/', 'Controller@create')->name('sessions.progress.create');

                                Route::prefix('{progressId}')->group(
                                    function () {
                                        Route::get('/', 'Controller@show')->name(ProgressDTO::ROUTE_NAME_SHOW);
                                        Route::delete('/', 'Controller@remove')->name('sessions.progress.remove');
                                    }
                                );
                            }
                        );
                    }
                );
            }
        );
    }
);
