<?php

namespace App\Components\Services\Http\Controllers;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Services\Services\Traits\ServiceServiceTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Arr;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Services\Http\Controllers
 */
class Controller extends BaseController
{
    use ServiceServiceTrait;
    use AuthServiceTrait;

    /**
     * @param Request $request
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function index(Request $request): Response
    {
        $this->serviceService__()->applyFilters($request->get('filter', []));

        return $this->sendResponse($this->serviceService__()->list());
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function create(JsonApi $jsonApi): Response
    {
        return $this->sendResponse(
            $this->serviceService__()->create(
                $this->authService__()->user()->readonly(),
                $jsonApi->attributes()->toArray()
            )->dto(),
            201
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $id
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NonUniqueResultException|NotFoundException|PropertyNotInit|UnexpectedValueException|RuntimeException
     */
    public function change(JsonApi $jsonApi, string $id): Response
    {
        return $this->sendResponse(
            $this->serviceService__()->workWith($id)->change($jsonApi->attributes()->toArray())->dto()
        );
    }

    /**
     * @param string $id
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     * @throws RuntimeException
     */
    public function show(string $id): Response
    {
        return $this->sendResponse($this->serviceService__()->workWith($id)->dto());
    }

    /**
     * @param string $id
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function remove(string $id): Response
    {
        $this->serviceService__()->workWith($id)->remove();

        return $this->acknowledgeResponse();
    }
}
