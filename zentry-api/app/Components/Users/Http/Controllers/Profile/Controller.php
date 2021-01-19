<?php

namespace App\Components\Users\Http\Controllers\Profile;

use App\Assistants\QR\Services\Traits\QRServiceTrait;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Profile
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use QRServiceTrait;
    use UserServiceTrait;
    use LinkParametersTrait;

    /**
     * @param Request $request
     * @param string  $userId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws NotFoundException
     */
    public function show(Request $request, string $userId): Response
    {
        $this->linkParameters__()->put(collect(['userId' => $this->authService__()->user()->identity()->toString()]));

        return $this->sendResponse($this->userService__()->workWith($userId)->profileDTO());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws NotFoundException
     */
    public function change(JsonApi $jsonApi, string $userId): Response
    {
        $this->linkParameters__()->put(collect(['userId' => $this->authService__()->user()->identity()->toString()]));

        return $this->sendResponse(
            $this->userService__()->workWith($userId)->changeProfile(
                new Payload(
                    $jsonApi->attributes()->get('first_name', ''),
                    $jsonApi->attributes()->get('last_name', ''),
                    $jsonApi->attributes()->get('phone_code', ''),
                    $jsonApi->attributes()->get('phone_number', '')
                )
            )->profileDTO()
        );
    }
}
