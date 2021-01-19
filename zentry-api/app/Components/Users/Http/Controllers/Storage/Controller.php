<?php

namespace App\Components\Users\Http\Controllers\Storage;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\Storage\StorageDTO;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Storage
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use UserServiceTrait;
    use LinkParametersTrait;

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
        $this->linkParameters__()->put(collect(['userId' => $this->authService__()->user()->identity()->toString()]));

        $storageService = $this->userService__()->workWith($this->authService__()->user()->identity())->storageService(
        );

        $storageService->listRO()->filter(function(StorageReadonlyContract $storage) {
            return !$storage->isDriver(StorageReadonlyContract::DRIVER_DEFAULT);
        })->each(
            function (StorageReadonlyContract $storage) {
                try {
                    $this->userService__()->storageService()->workWith($storage->identity())->sync();
                } catch (Exception $exception) {
                    app(ExceptionHandler::class)->report($exception);
                }
            }
        );

        return $this->sendResponse(
            $storageService->list()
        );
    }

    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function drivers(): Response
    {
        return $this->sendResponse(
            $this->userService__()->workWith($this->authService__()->user()->identity())->storageService()->drivers()
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
        $this->linkParameters__()->put(collect(['userId' => $this->authService__()->user()->identity()->toString()]));

        return $this->sendResponse(
            $this->userService__()->workWith($this->authService__()->user()->identity())->storageService()->create(
                $jsonApi->attributes()->toArray()
            )->dto(),
            201
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     * @param string  $storageId
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
    public function enable(JsonApi $jsonApi, string $userId, string $storageId): Response
    {
        $this->linkParameters__()->put(collect(['userId' => $this->authService__()->user()->identity()->toString()]));

        $this->userService__()->workWith($this->authService__()->user()->identity())->storageService()->workWith(
            $storageId
        )->change(
            ['enabled' => true]
        );

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     * @param string  $storageId
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
    public function delete(JsonApi $jsonApi, string $userId, string $storageId): Response
    {
        $this->linkParameters__()->put(collect(['userId' => $this->authService__()->user()->identity()->toString()]));

        $this->userService__()->workWith($this->authService__()->user()->identity())->storageService()->workWith(
            $storageId
        )->remove();

        return $this->acknowledgeResponse();
    }
}
