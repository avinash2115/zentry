<?php

namespace App\Components\Users\Http\Controllers\DataProvider;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Jobs\DataProvider\Synchronize;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\DataProvider
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use UserServiceTrait;

    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function index(): Response
    {
        return $this->sendResponse(
            $this->userService__()->workWith($this->authService__()->user()->identity())->dataProviderService(
            )->list()
        );
    }

    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function drivers(): Response
    {
        return $this->sendResponse(
            $this->userService__()->workWith($this->authService__()->user()->identity())->dataProviderService()->drivers()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function create(JsonApi $jsonApi, string $userId): Response
    {
        return $this->sendResponse(
            $this->userService__()->workWith($this->authService__()->user()->identity())->dataProviderService()->create(
                $jsonApi->attributes()->toArray()
            )->dto(),
            201
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     * @param string  $dataProviderId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function change(JsonApi $jsonApi, string $userId, string $dataProviderId): Response
    {
        return $this->sendResponse($this->userService__()->workWith($this->authService__()->user()->identity())->dataProviderService()->workWith(
            $dataProviderId
        )->change(
            $jsonApi->attributes()->toArray()
        )->dto());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     * @param string  $dataProviderId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function sync(JsonApi $jsonApi, string $userId, string $dataProviderId): Response
    {
        dispatch(new Synchronize($this->authService__()->user()->identity(), $this->userService__()->workWith($this->authService__()->user()->identity())->dataProviderService()->workWith(
            $dataProviderId
        )->identity()));

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     * @param string  $dataProviderId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function remove(JsonApi $jsonApi, string $userId, string $dataProviderId): Response
    {
        $this->userService__()->workWith($this->authService__()->user()->identity())->dataProviderService()->workWith(
            $dataProviderId
        )->remove();

        return $this->acknowledgeResponse();
    }
}
