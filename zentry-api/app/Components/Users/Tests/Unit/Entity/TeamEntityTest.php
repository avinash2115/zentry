<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Sessions\Tests\Unit\Traits\SessionHelperTestTrait;
use App\Components\Users\Team\Request\RequestContract;
use App\Components\Users\Team\TeamContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContectInvalidArgumentException;
use Tests\TestCase;

/**
 * Class TeamEntityTest
 */
class TeamEntityTest extends TestCase
{
    use SessionHelperTestTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->randString(), $this->randString(),],
            [$this->randString(5), null],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            ['', InvalidArgumentException::class, null],
        ];
    }

    /**
     * @param string $name
     * @param string $description
     * @param string $error
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(string $name, string $error, string $description = null): void
    {
        $this->expectException($error);
        $owner = $this->createUser();

        $this->make($this->generateIdentity(), $owner, $name, $description);
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws RecursionContectInvalidArgumentException
     * @throws RuntimeException
     * @throws NotFoundException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $name,
        string $description = null
    ): void {
        $identity = $this->generateIdentity();
        $owner = $this->createUser();

        $entity = $this->make($identity, $owner, $name, $description);

        static::assertTrue($entity->identity()->equals($identity));
        static::assertEquals($entity->name(), $name);
        static::assertEquals($entity->description(), $description);

        static::assertNotNull($entity->createdAt());

        self::assertCount(1, $entity->members());
        self::assertCount(0, $entity->requests());

        $user = $this->createUser();

        $request = app()->make(RequestContract::class, [
            'identity' => IdentityGenerator::next(),
            'user' => $user,
            'team' => $entity
        ]);

        $entity->addRequest($request);
        self::assertCount(1, $entity->requests());

        $entity->addRequest(app()->make(RequestContract::class, [
            'identity' => IdentityGenerator::next(),
            'user' => $owner,
            'team' => $entity
        ]));

        self::assertCount(1, $entity->requests());
        $entity->removeRequest($request);
        self::assertCount(0, $entity->requests());
    }

    /**
     * @param Identity             $id
     * @param UserReadonlyContract $owner
     * @param string               $name
     * @param string|null          $description
     *
     * @return TeamContract
     * @throws BindingResolutionException
     */
    protected function make(
        Identity $id,
        UserReadonlyContract $owner,
        string $name,
        string $description = null
    ): TeamContract {
        return app()->make(
            TeamContract::class,
            [
                'identity' => $id,
                'owner' => $owner,
                'name' => $name,
                'description' => $description,
            ]
        );
    }
}
