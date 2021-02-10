<?php

namespace App\Http\Controllers;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\Traits\PresenterTrait;
use App\Convention\ValueObjects\Meta\Meta;
use App\Convention\ValueObjects\Meta\Pagination;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use PresenterTrait;

    /**
     * @param mixed $data
     * @param int   $code
     * @param array $headers
     * @param array $meta
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function sendResponse($data = [], int $code = 200, array $headers = [], array $meta = []): Response
    {
        $pagination = app()->make(Meta::class)->pagination();

        if ($pagination instanceof Pagination) {
            $meta = array_merge(
                $meta,
                [
                    'pagination' => $pagination->toArray(),
                ]
            );
        }


        if ($data instanceof Collection || $data instanceof PresenterContract) {
            $data = $this->presenter__()->present($data, $meta);
        } else {
            $data = array_merge($data, $meta);
        }

        $response = new Response($data, $code);

        if ($headers) {
            collect($headers)->each(
                function ($value, $header) use (&$response) {
                    $response->header($header, $value);
                }
            );
        }

        return $response;
    }

    /**
     * @param bool $acknowledge
     * @param int  $code
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function acknowledgeResponse(bool $acknowledge = true, int $code = 200): Response
    {
        return $this->sendResponse(['acknowledge' => $acknowledge], $code);
    }
}
