<?php

namespace App\Assistants\Files\Http\Controllers;

use App\Assistants\Files\Exceptions\Temporary\Url\NotFoundOrExpired;
use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\LocalFileServiceTrait;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

/**
 * Class Controller
 *
 * @package App\Components\Files\Http\Controllers
 */
class Controller extends BaseController
{
    use FileServiceTrait;

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws FileNotFoundException
     * @throws NotFoundOrExpired
     * @throws \RuntimeException
     */
    public function downloadViaTemporaryURL(Request $request, string $id): Response
    {
        $subject = $this->fileService__()->downloadViaTemporaryURL($id);

        $response = app(ResponseFactory::class)->make($subject->content());

        collect($subject->headers())->each(
            static function ($value, $header) use ($response) {
                $response->header($header, $value);
            }
        );

        return $response;
    }
}
