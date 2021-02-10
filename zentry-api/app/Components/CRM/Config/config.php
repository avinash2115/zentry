<?php

use App\Components\Users\User\CRM\CRMReadonlyContract;

return [
    'crms' => [
        'drivers' => [
            CRMReadonlyContract::DRIVER_THERAPYLOG => [
                'type' => CRMReadonlyContract::DRIVER_THERAPYLOG,
                'title' => CRMReadonlyContract::LABEL_THERAPYLOG,
                'config' => [
                    'email' => [
                        'title' => 'Email',
                        'encryption' => false,
                        'hidden' => false,
                    ],
                    'password' => [
                        'title' => 'Password',
                        'encryption' => true,
                        'hidden' => true,
                    ],
                ],
            ],
        ],
    ],
];
