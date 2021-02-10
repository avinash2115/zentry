<?php

use App\Components\Users\Device\DeviceDTO;
use App\Components\Users\Participant\Goal\GoalDTO;
use App\Components\Users\Participant\Goal\Tracker\TrackerDTO;
use App\Components\Users\Participant\IEP\IEPDTO;
use App\Components\Users\Participant\ParticipantDTO;
use App\Components\Users\Participant\Therapy\TherapyDTO;
use App\Components\Users\Services\Device\DeviceServiceContract;
use App\Components\Users\User\DataProvider\DataProviderDTO;
use App\Components\Users\User\Storage\StorageDTO;
use App\Components\Users\User\UserDTO;
use App\Http\Middleware\Access\Device\Authenticate as AuthenticateDevice;
use App\Http\Middleware\Access\JWT\AuthenticateOrLogin;

Route::group(
    [
        'middleware' => [
            'web',
            'content',
        ],
    ],
    function () {
        Route::prefix('auth')->namespace('App\Components\Users\Http\Controllers\Auth')->group(
            function () {
                /** AUTHORIZATION START*/

                Route::prefix('login')->namespace('Login')->group(
                    function () {
                        Route::post('/', 'Controller@login')->name('auth.login');
                        Route::namespace('Token')->prefix('token')->group(
                            function () {
                                Route::post('/', 'Controller@create')->name('auth.login.token.create')->middleware(
                                    AuthenticateOrLogin::ALIAS
                                );

                                Route::post('{tokenId}', 'Controller@signin')->name('auth.login.token.signin');
                            }
                        );
                    }
                );

                Route::prefix('sso')->namespace('SSO')->group(
                    function () {
                        Route::post('/', 'Controller@make')->name('auth.sso.make');
                        Route::get('/drivers', 'Controller@drivers')->name('auth.sso.drivers');
                    }
                );

                // add jwt midlleware
                Route::get('logout', 'Login\Controller@logout')->name('auth.logout')->middleware('jwt-auth');

                Route::post('signup', 'SignupController@signup')->name('auth.signup');

                // add jwt midlleware
                Route::post('token/refresh', 'Login\Controller@refreshToken')->name('auth.renew')->middleware(
                    'jwt-auth-refresh'
                );
                /** AUTHORIZATION ENDS*/
            }
        );

        Route::prefix('password_reset')->namespace('App\Components\Users\Http\Controllers\PasswordReset')->group(
            function () {
                /** PASSWORD RESET START */
                Route::post('/activate', 'Controller@activate')->name('password_reset.activate')->middleware(
                    'jwt-auth'
                );

                Route::post('/', 'Controller@create')->name('password_reset.create');

                Route::prefix('{passwordResetId}')->group(
                    function () {
                        Route::get('/', 'Controller@show')->name('password_reset.show');

                        Route::post('/', 'Controller@update')->name('password_reset.update');
                    }
                );
            }
        );
        /** PASSWORD RESET END */
        Route::prefix('users')->namespace(
            'App\Components\Users\Http\Controllers',
            )->middleware('jwt-auth')->group(
            function () {
                Route::get('current', 'UserController@current')->name(UserDTO::ROUTE_NAME_SHOW)->middleware(
                    AuthenticateDevice::ALIAS
                );

                Route::prefix('{userId}')->group(
                    function () {
                        Route::get('/', 'UserController@show')->name(UserDTO::ROUTE_NAME_SHOW);
                        Route::post('/', 'UserController@change')->name('users.change');

                        Route::prefix('relationships/profile')->namespace(
                            'Profile',
                            )->group(
                            function () {
                                Route::get('/', 'Controller@show')->name('users.profile.show');
                                Route::post('/', 'Controller@change')->name('users.profile.change');
                            }
                        );

                        Route::prefix('relationships/storages')->namespace(
                            'Storage',
                            )->group(
                            function () {
                                Route::get('/', 'Controller@index')->name('users.storages.index');
                                Route::post('/', 'Controller@create')->name('users.storages.create');

                                Route::get('/drivers', 'Controller@drivers')->name('users.storages.drivers');

                                Route::prefix('{storageId}')->group(
                                    function () {
                                        Route::get('/', function () { })->name(StorageDTO::ROUTE_NAME_SHOW);
                                        Route::delete('/', 'Controller@remove')->name('users.storages.remove');
                                        Route::post('/enable', 'Controller@enable')->name('users.storages.enable');
                                    }
                                );
                            }
                        );

                        Route::prefix('relationships/crms')->namespace(
                            'CRM',
                            )->group(
                            function () {
                                Route::get('/', 'Controller@index')->name('users.crms.index');
                                Route::post('/', 'Controller@create')->name('users.crms.create');

                                Route::get('/drivers', 'Controller@drivers')->name('users.crms.drivers');
                                Route::post('/sync/{type?}', 'Controller@syncFull')->name('users.crms.sync.full');

                                Route::prefix('{crmId}')->group(function () {
                                    Route::get('/', function () { })->name(\App\Components\Users\User\CRM\CRMDTO::ROUTE_NAME_SHOW);
                                    Route::post('/', 'Controller@change')->name('users.crms.change');
                                    Route::delete('/', 'Controller@remove')->name('users.crms.remove');
                                    Route::post('/sync/{type?}', 'Controller@sync')->name('users.crms.sync');
                                    Route::get('/sync/log/{type}', 'Controller@syncLog')->name('users.crms.sync.log');
                                });
                            }
                        );

                        Route::prefix('relationships/data_providers')->namespace(
                            'DataProvider',
                            )->group(
                            function () {
                                Route::get('/', 'Controller@index')->name('users.data_provider.index');
                                Route::post('/', 'Controller@create')->name('users.data_provider.create');

                                Route::get('/drivers', 'Controller@drivers')->name('users.data_provider.drivers');

                                Route::prefix('{dataProviderId}')->group(function () {
                                    Route::get('/', function () {})->name(DataProviderDTO::ROUTE_NAME_SHOW);
                                    Route::delete('/', 'Controller@remove')->name('users.data_provider.remove');
                                    Route::post('/', 'Controller@change')->name('users.data_provider.change');
                                    Route::post('sync', 'Controller@sync')->name('users.data_provider.sync');
                                });
                            }
                        );
                    }
                );
            }
        );

        Route::prefix('devices')->middleware('jwt-auth')->namespace(
            'App\Components\Users\Http\Controllers\Device',
            )->group(
            function () {
                Route::get('/', 'Controller@index')->name('devices.index');

                Route::post('connect/{token}', 'Controller@connectByToken')->name(
                    DeviceServiceContract::ROUTE_ADD_DEVICE_BY_TOKEN
                )->withoutMiddleware('jwt-auth');

                Route::delete('/', 'Controller@removeCurrent')->name('devices.remove_current')->middleware(
                    AuthenticateDevice::ALIAS
                );

                Route::get('qr', 'Controller@qr')->name('devices.qr')->withoutMiddleware('content');

                Route::prefix('{deviceId}')->group(
                    function () {
                        Route::get('/', 'Controller@show')->name(DeviceDTO::ROUTE_NAME_SHOW);
                        Route::delete('/', 'Controller@remove')->name('devices.remove');
                    }
                );
            }
        );

        Route::prefix('participants')->middleware('jwt-auth')->namespace(
            'App\Components\Users\Http\Controllers\Participant',
            )->group(
            function () {
                Route::get('/', 'Controller@index')->name('users.participants.index')->middleware(
                    AuthenticateDevice::ALIAS
                );

                Route::post('/', 'Controller@create')->name(
                    'users.participants.create'
                )->middleware(AuthenticateDevice::ALIAS);

                Route::prefix('{participantId}')->group(
                    function () {
                        Route::get('/', 'Controller@show')->name(ParticipantDTO::ROUTE_NAME_SHOW);
                        Route::post('/', 'Controller@change')->name('users.participants.change');
                        Route::delete('/', 'Controller@remove')->name('users.participants.remove');
                        Route::post('/merge', 'Controller@merge')->name('users.participants.merge');

                        Route::prefix('relationships/therapy')->group(
                            function () {
                                Route::get('/', 'Controller@therapyShow')->name(TherapyDTO::ROUTE_NAME_SHOW);
                                Route::post('/', 'Controller@therapyChange')->name('users.participants.therapy.change');
                            }
                        );

                        Route::prefix('relationships/goals')->namespace(
                            'Goal',
                            )->group(
                            function () {
                                Route::get('/', 'Controller@index')->name('users.participants.goals.index')->middleware(
                                    AuthenticateDevice::ALIAS
                                );

                                Route::post('/', 'Controller@create')->name(
                                    'users.participants.goals.create'
                                )->middleware(AuthenticateDevice::ALIAS);

                                Route::prefix('{goalId}')->group(
                                    function () {
                                        Route::get('/', 'Controller@show')->name(GoalDTO::ROUTE_NAME_SHOW);
                                        Route::post('/', 'Controller@change')->name('users.participants.goals.change');
                                        Route::delete('/', 'Controller@remove')->name(
                                            'users.participants.goals.remove'
                                        );

                                        Route::prefix('relationships/tracker')->namespace(
                                            'Tracker',
                                            )->group(
                                            function () {
                                                Route::get('/', 'Controller@index')->name('users.participants.goals.trackers.index')->middleware(
                                                    AuthenticateDevice::ALIAS
                                                );

                                                Route::post('/', 'Controller@create')->name(
                                                    'users.participants.goals.trackers.create'
                                                )->middleware(AuthenticateDevice::ALIAS);


                                                Route::prefix('{trackerId}')->group(
                                                    function () {
                                                        Route::get('/', 'Controller@show')->name(
                                                            TrackerDTO::ROUTE_NAME_SHOW
                                                        );
                                                        Route::post('/', 'Controller@change')->name(
                                                            'users.participants.goals.trackers.change'
                                                        );
                                                        Route::delete('/', 'Controller@remove')->name(
                                                            'users.participants.goals.trackers.remove'
                                                        );
                                                    });
                                            }
                                        );
                                    }
                                );
                            }
                        );

                        Route::prefix('relationships/ieps')->namespace(
                            'IEP',
                        )->group(
                            function () {
                                Route::get('/', 'Controller@index')->name('users.participants.ieps.index')->middleware(
                                    AuthenticateDevice::ALIAS
                                );

                                Route::post('/', 'Controller@create')->name(
                                    'users.participants.ieps.create'
                                )->middleware(AuthenticateDevice::ALIAS);

                                Route::prefix('{iepId}')->group(
                                    function () {
                                        Route::get('/', 'Controller@show')->name(IEPDTO::ROUTE_NAME_SHOW);
                                        Route::post('/', 'Controller@change')->name('users.participants.ieps.change');
                                        Route::delete('/', 'Controller@remove')->name(
                                            'users.participants.ieps.remove'
                                        );
                                    }
                                );
                            }
                        );
                    }
                );
            }
        );

        Route::prefix('teams')->middleware(['jwt-auth', AuthenticateDevice::ALIAS])->namespace(
            'App\Components\Users\Http\Controllers\Team',
            )->group(
            function () {
                Route::get('/', 'Controller@index')->name('users.teams.index');
                Route::post('/', 'Controller@create')->name('users.teams.create');

                Route::prefix('{teamId}')->group(
                    function () {
                        Route::get('/', 'Controller@show')->name('users.teams.show');
                        Route::post('/', 'Controller@change')->name('users.teams.change');
                        Route::delete('/', 'Controller@remove')->name('users.teams.remove');
                        Route::delete('leave', 'Controller@leave')->name('users.teams.leave');

                        Route::prefix('relationships/requests')->namespace(
                            'Request',
                            )->group(
                            function () {
                                Route::get('/', 'Controller@index')->name('users.teams.requests.index');
                                Route::post('/', 'Controller@create')->name('users.teams.requests.create');
                                Route::prefix('{requestId}')->group(
                                    function () {
                                        Route::get('/', 'Controller@show')->name('users.teams.requests.show');
                                        Route::post('apply', 'Controller@apply')->name('users.teams.requests.apply');
                                        Route::post('reject', 'Controller@reject')->name('users.teams.requests.reject');
                                        Route::delete('/', 'Controller@remove')->name('users.teams.requests.remove');
                                    }
                                );
                            }
                        );

                        Route::prefix('relationships/schools')->namespace(
                            'School',
                            )->group(
                            function () {
                                Route::get('/', 'Controller@index')->name('users.teams.schools.index');
                                Route::post('/', 'Controller@create')->name('users.teams.schools.create');

                                Route::prefix('{schoolId}')->group(
                                    function () {
                                        Route::get('/', 'Controller@show')->name('users.teams.schools.show');
                                        Route::post('/', 'Controller@change')->name('users.teams.schools.change');
                                        Route::delete('/', 'Controller@remove')->name('users.teams.schools.remove');
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
