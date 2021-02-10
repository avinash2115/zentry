<?php

namespace App\Components\Share\Http\Controllers\Shared;

use App\Components\Share\Services\Shared\Traits\SharedServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Share\Http\Controllers\Shared
 */
class Controller extends BaseController
{
    use SharedServiceTrait;

    /**
     * @param string  $id
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     */
    public function show(string $id): Response
    {
        return $this->sendResponse(
            $this->sharedService__()->workWith($id)->dto()
        );
    }

}
