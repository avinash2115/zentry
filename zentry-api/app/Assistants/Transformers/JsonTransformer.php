<?php

namespace App\Assistants\Transformers;

use App\Assistants\Transformers\Contracts\TransformerContract;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Assistants\Transformers\ValueObjects\JsonApiResponseBuilder;
use App\Convention\ValueObjects\Meta\Meta;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;
use UnexpectedValueException;

/**
 * Class JsonTransformer
 *
 * @package App\Assistants\Transformers
 */
class JsonTransformer implements TransformerContract
{
    /**
     * @param Request $request
     *
     * @return Request
     * @throws UnexpectedValueException
     */
    public function from(Request $request): Request
    {
        if ($request->isMethod('GET')) {
            app()->singleton(
                JsonApiResponseBuilder::class,
                function () use ($request) {
                    return new JsonApiResponseBuilder(
                        $request->get('include', ''),
                        $request->get('fields', []),
                        $request->get('sort_by', [])
                    );
                }
            );

            app()->singleton(Meta::class, fn() => new Meta());
        } else {
            app()->singleton(
                JsonApi::class,
                function () use ($request) {
                    return new JsonApi(
                        collect($request->toArray())
                    );
                }
            );

            app()->singleton(
                JsonApiResponseBuilder::class,
                function () {
                    return new JsonApiResponseBuilder('*', []);
                }
            );
        }

        return $request;
    }

    /**
     * @param array $data
     *
     * @return string
     * @throws RuntimeException|Throwable
     */
    public function to(array $data): string
    {
        $result = json_encode($data);

        if (!is_string($result)) {
            throw new RuntimeException('Transformer cannor convert data to json');
        }

        return $result;
    }
}
