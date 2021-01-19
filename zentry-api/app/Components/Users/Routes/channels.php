<?php

use App\Components\Users\Services\Auth\AuthUserServiceContract;

Broadcast::channel(
    'users-{userId}.devices',
    function (
        AuthUserServiceContract $auth,
        string $userId
    ) {
        return $auth->identity()->toString() === $userId;
    }
);
