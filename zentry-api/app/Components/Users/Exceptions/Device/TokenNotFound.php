<?php

namespace App\Components\Users\Exceptions\Device;

use App\Convention\Exceptions\Repository\NotFoundException;

/**
 * Class TokenNotFound
 *
 * @package App\Components\Users\Exceptions\Device
 */
class TokenNotFound extends NotFoundException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(
        $message = 'Token not found',
        $code = 404
    ) {
        parent::__construct($message, $code);
    }
}
