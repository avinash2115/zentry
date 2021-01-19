<?php

namespace App\Assistants\Transformers\Tests\Unit;

use App\Assistants\Transformers\Presenter;
use App\Assistants\Transformers\ValueObjects\JsonApiResponseBuilder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class PresenterTest
 *
 * @package App\Assistants\Transformers\Tests\Unit
 */
class PresenterTest extends TestCase
{
    /**
     * @throws BindingResolutionException
     */
    public function testSingleDTO(): void
    {
        $dto = new ExamplePresenterClass();
        $nestedDTO = new NestedExamplePresenterClass();
        $exampleAttrs = [
            'name' => Str::random(),
            'title' => Str::random(),
        ];
        $nestedAttrs = [
            'name' => Str::random(),
            'title' => Str::random(),
        ];

        /**
         * @var Presenter $presenter
         */
        $presenter = $this->app->make(Presenter::class);

        $data = $presenter->present($dto);

        $this->assertSame($data['data'], $dto->present());

        $dto->setAttributes($exampleAttrs);
        $data = $presenter->present($dto);

        $this->assertSame($data['data'], array_merge($dto->present(), ['attributes' => $dto->attributes]));

        $dto->setRelationships(
            [
                'nested' => $nestedDTO,
            ]
        );

        $data = $presenter->present($dto);

        $this->assertSame($data['data']['relationships']['nested']['data'], $nestedDTO->present());

        $nestedDTO->setAttributes($nestedAttrs);
        $dto->setRelationships(
            [
                'nested' => $nestedDTO,
            ]
        );
        $this->app->singleton(
            JsonApiResponseBuilder::class,
            function () {
                return new JsonApiResponseBuilder('*', []);
            }
        );
        $presenter = $this->app->make(Presenter::class);

        $data = $presenter->present($dto);

        $this->assertSame(
            $data['data']['relationships']['nested']['data'],
            array_merge($nestedDTO->present(), ['attributes' => $nestedDTO->attributes])
        );
    }
}
