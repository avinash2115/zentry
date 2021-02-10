<?php

namespace App\Components\Users\Http\Controllers\Team;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Team
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use UserServiceTrait;
    use LinkParametersTrait;
    use TeamServiceTrait;

    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     * @throws NotImplementedException
     */
    public function index(): Response
    {
        return $this->sendResponse($this->teamService__()->list());
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function create(JsonApi $jsonApi): Response
    {
        return $this->sendResponse($this->teamService__()->create($jsonApi->attributes()->toArray())->dto(), 201);
    }

    /**
     * @param string $teamId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function show(string $teamId): Response
    {
        return $this->sendResponse(
            $this->teamService__()->workWith($teamId)->dto()
        );
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
    public function change(JsonApi $jsonApi, string $teamId): Response
    {
        return $this->sendResponse(
            $this->teamService__()->workWith($teamId)->change($jsonApi->attributes()->toArray())->dto()
        );
    }

    /**
     * @param string $teamId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     */
    public function remove(string $teamId): Response
    {
        $this->teamService__()->workWith($teamId)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @param string $teamId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function leave(string $teamId): Response
    {
        $this->teamService__()->workWith($teamId)->leave();

        return $this->acknowledgeResponse();
    }
}
