<?php

use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Users\Services\Auth\AuthUserServiceContract;

Broadcast::channel(
    SessionServiceContract::BROADCAST_CHANNEL_BASE,
    function (
        AuthUserServiceContract $auth,
        string $userId
    ) {
        return $auth->identity()->toString() === $userId;
    }
);

Broadcast::channel(
    SessionServiceContract::BROADCAST_CHANNEL_EXACT,
    function (
        AuthUserServiceContract $auth,
        string $userId,
        string $sessionId
    ) {
        return $auth->identity()->toString() === $userId;
    }
);
