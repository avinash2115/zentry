<?php

namespace App\Components\Users\Http\Controllers\Team\School;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Team\School
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use UserServiceTrait;
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
        return $this->sendResponse($this->teamService__()->workWith($teamId)->schoolService()->list());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $teamId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PermissionDeniedException
     */
    public function create(JsonApi $jsonApi, string $teamId): Response
    {
        return $this->sendResponse(
            $this->teamService__()->workWith($teamId)->schoolService()->create($jsonApi->attributes()->toArray())->dto(
            ),
            201
        );
    }

    /**
     * @param string $teamId
     * @param string $schoolId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PermissionDeniedException
     */
    public function show(string $teamId, string $schoolId): Response
    {
        return $this->sendResponse(
            $this->teamService__()->workWith($teamId)->schoolService()->workWith($schoolId)->dto()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $teamId
     * @param string  $schoolId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PermissionDeniedException
     */
    public function change(JsonApi $jsonApi, string $teamId, string $schoolId): Response
    {
        $dto = $this->teamService__()->workWith($teamId)->schoolService()->workWith($schoolId)->change(
            $jsonApi->attributes()->toArray()
        )->dto();

        if ($jsonApi->relation('target_team') instanceof JsonApi) {
            $school = $this->teamService__()->schoolService()->readonly();
            $targetTeam = $this->teamService__()->workWith($jsonApi->relation('target_team')->id())->readonly();

            $this->teamService__()->workWith($teamId)->moveSchoolTo($school, $targetTeam);
            $dto = $this->teamService__()->workWith($targetTeam->identity())->schoolService()->workWith(
                $school->identity()
            )->dto();
        }

        return $this->sendResponse($dto);
    }

    /**
     * @param string $teamId
     * @param string $schoolsId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PermissionDeniedException
     */
    public function remove(string $teamId, string $schoolsId): Response
    {
        $this->teamService__()->workWith($teamId)->schoolService()->workWith($schoolsId)->remove();

        return $this->acknowledgeResponse();
    }
}
