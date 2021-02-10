<?php

namespace App\Components\Users\Http\Controllers\Auth\Traits;

/**
 * Trait AuthControllerTrait
 *
 * @package App\Components\Users\Http\Controllers\Auth\Traits
 */
trait AuthControllerTrait
{
    /**
     * @param string $token
     *
     * @return array
     */
    private function getSessionResponse(string $token): array
    {
        return [
            'data' => [
                'type' => 'session',
                'attributes' => [
                    'token' => $token,
                ],
            ],
        ];
    }
}