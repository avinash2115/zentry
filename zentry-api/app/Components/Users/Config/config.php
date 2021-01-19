<?php

use App\Components\Users\Services\Auth\AuthServiceContract;
use App\Components\Users\Services\User\DataProvider\Sync\GoogleCalendar;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use App\Components\Users\User\Storage\StorageReadonlyContract;

return [
    'storages' => [
        'drivers' => [
            StorageReadonlyContract::DRIVER_DEFAULT => [
                'type' => StorageReadonlyContract::DRIVER_DEFAULT,
                'title' => StorageReadonlyContract::LABEL_DEFAULT,
                'config' => [
                ],
            ],
            StorageReadonlyContract::DRIVER_KLOUDLESS_GOOGLE_DRIVE => [
                'type' => StorageReadonlyContract::DRIVER_KLOUDLESS_GOOGLE_DRIVE,
                'title' => StorageReadonlyContract::LABEL_KLOUDLESS_GOOGLE_DRIVE,
                'config' => [
                    'id',
                ],
            ],
            StorageReadonlyContract::DRIVER_KLOUDLESS_DROPBOX => [
                'type' => StorageReadonlyContract::DRIVER_KLOUDLESS_DROPBOX,
                'title' => StorageReadonlyContract::LABEL_KLOUDLESS_DROPBOX,
                'config' => [
                    'id',
                ],
            ],
            StorageReadonlyContract::DRIVER_KLOUDLESS_BOX => [
                'type' => StorageReadonlyContract::DRIVER_KLOUDLESS_BOX,
                'title' => StorageReadonlyContract::LABEL_KLOUDLESS_BOX,
                'config' => [
                    'id',
                ],
            ],
        ],
    ],

    'sso' => [
        'drivers' => [
            AuthServiceContract::SSO_DRIVER_GOOGLE => [
                'type' => AuthServiceContract::SSO_DRIVER_GOOGLE,
                'title' => ucfirst(AuthServiceContract::SSO_DRIVER_GOOGLE),
                'config' => [
                    'authToken'
                ],
            ],
        ],
    ],

    'data_providers' => [
        'drivers' => [
            DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR => [
                'type' => DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR,
                'title' => DataProviderReadonlyContract::LABEL_GOOGLE_CALENDAR,
                'config' => [
                    DataProviderReadonlyContract::CONFIG_AUTH_CODE_KEY,
                    DataProviderReadonlyContract::CONFIG_EMAIL_KEY,
                ],
            ],
        ],
        DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR => [
            GoogleCalendar::CONFIG_KEY_CREDENTIALS => env('GOOGLE_CALENDAR_CREDENTIALS_ABSOLUTE_PATH', ''),
            GoogleCalendar::CONFIG_KEY_REDIRECT_URI => env('DOMAIN_SCHEMA', '') . env('DOMAIN_BASE', ''),
        ]
    ],
];
