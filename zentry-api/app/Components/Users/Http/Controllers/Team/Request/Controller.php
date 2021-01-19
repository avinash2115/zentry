<?php

namespace App\Components\Users\Http\Controllers\Team\Request;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Team\Request
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use UserServiceTrait;
    use LinkParametersTrait;
    use TeamServiceTrait;

    /**
     * @param string $teamId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     */
    public function index(string $teamId): Response
    {
        return $this->sendResponse($this->teamService__()->workWith($teamId)->requestService()->list());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $teamId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function create(JsonApi $jsonApi, string $teamId): Response
    {
        $user = $jsonApi->relation('user');

        if (!$user instanceof JsonApi) {
            throw new InvalidArgumentException('User relationship is required for request creation');
        }

        return $this->sendResponse(
            $this->teamService__()->workWith($teamId)->requestService()->create(
                $this->userService__()->workWith($user->id())->readonly(),
                $jsonApi->attributes()->toArray()
            )->dto(), 201
        );
    }

    /**
     * @param string $teamId
     * @param string $requestId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function show(string $teamId, string $requestId): Response
    {
        return $this->sendResponse(
            $this->teamService__()->workWith($teamId)->requestService()->workWith($requestId)->dto()
        );
    }

    /**
     * @param string  $teamId
     * @param string  $requestId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function apply(string $teamId, string $requestId): Response
    {
        $this->teamService__()->workWith($teamId)->requestService()->workWith($requestId)->apply();

        return $this->acknowledgeResponse();
    }

    /**
     * @param string  $teamId
     * @param string  $requestId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function reject(string $teamId, string $requestId): Response
    {
        $this->teamService__()->workWith($teamId)->requestService()->workWith($requestId)->reject();

        return $this->acknowledgeResponse();
    }

    /**
     * @param string  $teamId
     * @param string  $requestId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     */
    public function remove(string $teamId, string $requestId): Response
    {
        $this->teamService__()->workWith($teamId)->requestService()->workWith($requestId)->remove();

        return $this->acknowledgeResponse();
    }
}
