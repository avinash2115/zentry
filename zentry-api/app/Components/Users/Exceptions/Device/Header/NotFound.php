<?php

namespace App\Components\Users\Exceptions\Device\Header;

use App\Convention\Exceptions\Repository\NotFoundException;

/**
 * Class NotFound
 *
 * @package App\Components\Users\Exceptions\Device\Header
 */
class NotFound extends NotFoundException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(
        $message = 'Device not found',
        $code = 404
    ) {
        parent::__construct($message, $code);
    }
}
