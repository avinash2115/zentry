<?php

namespace App\Assistants\Search\Http\Controllers;

use App\Assistants\Search\Agency\Controllers\Searchable;
use App\Assistants\Search\Services\Traits\SearchServiceTrait;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;

/**
 * Class SearchController
 *
 * @package App\Assistants\Search\Http\Controllers
 */
class Controller extends BaseController implements Searchable
{
    use SearchServiceTrait;

    /**
     * @param Request $request
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function search(Request $request): Response
    {
        $searchDTO = $this->searchService__()->search($request->input('term', ''));

        return $this->sendResponse($searchDTO);
    }

    /**
     * @inheritDoc
     */
    public function autocomplete(Request $request): Response
    {
        $result = $this->searchService__()->autocomplete($request->input('term', ''));

        return $this->sendResponse($result);
    }
}
