<?php

namespace App\Assistants\Transformers\Tests\Unit;

use App\Assistants\Transformers\ValueObjects\JsonApiResponseBuilder;
use Error;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Tests\TestCase;
use UnexpectedValueException;

/**
 * Class JsonResponseApiTest
 *
 * @package App\Assistants\Transformers\Tests\Unit
 */
class JsonResponseApiTest extends TestCase
{
    /**
     *
     */
    public function testJsonResponse(): void
    {
        $jsonResponse = new JsonApiResponseBuilder(
            'profile', [
            'profile' => 'name',
        ]
        );

        $this->assertTrue($jsonResponse->wantsInclude());
        $this->assertTrue($jsonResponse->wantsInclude('profile'));
        $this->assertTrue($jsonResponse->wantsSpecifiedFields());
        $this->assertTrue($jsonResponse->wantsSpecifiedFields('profile'));

        $this->assertFalse($jsonResponse->wantsAllNestedIncludes());

        $jsonResponseWithAsterisk = new JsonApiResponseBuilder(
            '*', [
            'profile' => 'name',
        ]
        );

        $this->assertTrue($jsonResponseWithAsterisk->wantsAllNestedIncludes());
        $this->assertTrue($jsonResponseWithAsterisk->wantsInclude('profile'));
        $this->assertTrue($jsonResponseWithAsterisk->wantsInclude(Str::random()));
        $this->assertTrue($jsonResponse->wantsSpecifiedFields());
        $this->assertTrue($jsonResponse->wantsSpecifiedFields('profile'));

        $stringName = 'nested.withNested.withNested';
        $jsonResponseWithNested = new JsonApiResponseBuilder(
            $stringName, [
            'profile' => 'test1,test2,test3',
        ]
        );

        $this->assertTrue(Arr::has($jsonResponseWithNested->includes()->toArray(), 'nested'));
        $this->assertTrue(Arr::has($jsonResponseWithNested->includes()->toArray(), $stringName));

        $this->assertTrue($jsonResponseWithNested->wantsSpecifiedFields());
        $this->assertTrue($jsonResponseWithNested->wantsSpecifiedFields('profile'));

        $fields = $jsonResponseWithNested->fields('profile');
        $this->assertEquals(
            $fields->toArray(),
            [
                'test1',
                'test2',
                'test3',
            ]
        );
        $this->expectException(UnexpectedValueException::class);
        new JsonApiResponseBuilder(
            'profile', [
                         'profile' => [
                             'name',
                         ],
                     ]
        );
    }

    /**
     * @dataProvider wrongIncludeData
     *
     * @param string $error
     * @param        $include
     * @param        $fields
     * @param        $includeNeeded
     * @param        $fieldNeeded
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function testWrongJsonGetter(string $error, $include, $fields, $includeNeeded, $fieldNeeded): void
    {
        $this->expectException($error);
        $jsonResponse = new JsonApiResponseBuilder($include, $fields);

        $jsonResponse->include($includeNeeded);
        $jsonResponse->fields($fieldNeeded);
    }

    /**
     * @return array
     */
    public function wrongIncludeData()
    {
        return [
            [Error::class, null, null, null, null],
            [Error::class, null, "", [], 1],
            [Error::class, 1, null, [], 1],
            [InvalidArgumentException::class, Str::random(), [], Str::random(), Str::random()],
            [InvalidArgumentException::class, '*', [], Str::random(), Str::random()],
        ];
    }
}
