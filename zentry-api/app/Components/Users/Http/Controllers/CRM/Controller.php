<?php

namespace App\Components\Users\Http\Controllers\CRM;

use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\CRM\CRMReadonlyContract;
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
 * @package App\Components\Users\Http\Controllers\CRM
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

        return $this->sendResponse(
            $this->userService__()->workWith($this->authService__()->user()->identity())->crmService()->list()
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
            $this->userService__()->workWith($this->authService__()->user()->identity())->crmService()->drivers()
        );
    }

    /**
     * @param JsonApi $jsonApi
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
    public function create(JsonApi $jsonApi): Response
    {
        $this->linkParameters__()->put(collect(['userId' => $this->authService__()->user()->identity()->toString()]));

        return $this->sendResponse(
            $this->userService__()->workWith($this->authService__()->user()->identity())->crmService()->connect(
                $jsonApi->attributes()->toArray()
            )->dto(),
            201
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     * @param string  $crmId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     */
    public function change(JsonApi $jsonApi, string $userId, string $crmId): Response
    {
        $this->linkParameters__()->put(collect(['userId' => $this->authService__()->user()->identity()->toString()]));

        return $this->sendResponse(
            $this->userService__()
                ->workWith($this->authService__()->user()->identity())
                ->crmService()
                ->workWith($crmId)
                ->change($jsonApi->attributes()->except(['active'])->toArray())
                ->dto()
        );
    }

    /**
     * @param string      $userId
     * @param string      $crmId
     * @param string|null $type
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function sync(string $userId, string $crmId, string $type = null): Response
    {
        $this->userService__()
            ->workWith($this->authService__()->user()->identity())
            ->crmService()
            ->workWith($crmId)
            ->sync($type);

        return $this->acknowledgeResponse();
    }

    /**
     * @param string      $userId
     * @param string|null $type
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function syncFull(string $userId, string $type = null): Response
    {
        $userService = $this->userService__()->workWith($this->authService__()->user()->identity());
        $userService->readonly()->crms()->each(
            function (CRMReadonlyContract $crm) use ($userService, $type) {
                $userService->crmService()->workWith($crm->identity())->sync($type);
            }
        );

        return $this->acknowledgeResponse();
    }

    /**
     * @param string $userId
     * @param string $crmId
     * @param string $type
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    public function syncLog(string $userId, string $crmId, string $type): Response
    {
        return $this->sendResponse(
            $this->userService__()
                ->workWith($this->authService__()->user()->identity())
                ->crmService()
                ->workWith($crmId)
                ->lastLog(
                    $type
                )
        );
    }
}
