<?php

namespace App\Assistants\Transformers\Tests\Unit;

use App\Assistants\Transformers\JsonApi\Attributes;
use App\Assistants\Transformers\JsonApi\Body;
use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Assistants\Transformers\JsonApi\Links;
use App\Assistants\Transformers\JsonApi\Relationships;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class JsonApiTest
 *
 * @package App\Assistants\Transformers\Tests\Unit
 */
class JsonApiTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAttributes(): void
    {
        $exampleDTO = new ExamplePresenterClass();
        $attributes = new Attributes($exampleDTO, collect([]));
        $this->assertTrue($attributes->isEmpty());

        $exampleDTO->setAttributes(
            [
                'test' => Str::random(),
                'test2' => Str::random(),
            ]
        );

        $filledAttributes = new Attributes($exampleDTO, collect(['test2']));
        $this->assertFalse($filledAttributes->isEmpty());
        $arrayAttributes = $filledAttributes->present()->get('attributes');
        $this->assertTrue(Arr::has($arrayAttributes, 'test2'));
        $this->assertFalse(Arr::has($arrayAttributes, 'test'));
        $emptyPresenter = new EmptyPresenter();
        $attributesEmpty = new Attributes($emptyPresenter, collect([]));

        $this->assertTrue($attributesEmpty->isEmpty());
    }

    /**
     *
     */
    public function testBodies(): void
    {
        $exampleDTO = new ExamplePresenterClass();
        $body = new Body($exampleDTO);
        $this->assertFalse($body->isEmpty());
        $this->assertEquals($body->present()->toArray(), $exampleDTO->present());

        $emptyPresenter = new EmptyPresenter();
        $bodyEmpty = new Body($emptyPresenter);

        $this->assertTrue($bodyEmpty->isEmpty());
    }

    /**
     *
     */
    public function testLinks(): void
    {
        $exampleDTO = new ExamplePresenterClass();
        $link = new Links(new LinkParameters(), $exampleDTO);
        $this->assertTrue($link->isEmpty());
        $exampleDTO->setLinks(
            [
                'self' => Str::random(),
            ]
        );
        $link2 = new Links(new LinkParameters(), $exampleDTO);

        $this->assertFalse($link2->isEmpty());
        $this->assertEquals($link2->present()->get('links'), $exampleDTO->relatedData(new LinkParameters())->toArray());

        $emptyPresenter = new EmptyPresenter();
        $linksEmpty = new Links(new LinkParameters(), $emptyPresenter);

        $this->assertTrue($linksEmpty->isEmpty());
    }

    /**
     * @throws BindingResolutionException
     */
    public function testRelationShips(): void
    {
        $exampleDTO = new ExamplePresenterClass();
        $relations = new Relationships(new LinkParameters(), $exampleDTO, null);

        $this->assertTrue($relations->isEmpty());
        $nestedDTO = new NestedExamplePresenterClass();
        $exampleDTO->setRequiredRelationships(
            [
                'nested' => $nestedDTO,
            ]
        );

        $filledRelationships = new Relationships(new LinkParameters(), $exampleDTO, collect([]));
        $this->assertFalse($filledRelationships->isEmpty());
        $filledPresent = $filledRelationships->present();
        $this->assertEquals(Arr::get($filledPresent, 'relationships.nested.data'), $nestedDTO->present());

        $emptyPresenter = new EmptyPresenter();
        $relationsEmpty = new Relationships(new LinkParameters(), $emptyPresenter, null);

        $this->assertTrue($relationsEmpty->isEmpty());
    }
}
