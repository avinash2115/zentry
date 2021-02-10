<?php

namespace App\Assistants\Search\Agency\Controllers;

use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Psy\Exception\FatalErrorException;

/**
 * Interface Searchable
 *
 * @package App\Assistants\Search\Agency\Controllers
 */
interface Searchable
{
    /**
     * @param Request $request
     *
     * @return Response
     * @throws NotFoundException
     * @throws FatalErrorException
     * @throws PermissionDeniedException
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function autocomplete(Request $request): Response;
}
