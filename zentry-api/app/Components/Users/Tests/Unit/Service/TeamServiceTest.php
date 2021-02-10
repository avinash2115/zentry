<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Team\TeamServiceContract;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\Team\TeamDTO;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Tests\Traits\HelperTrait;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;

/**
 * Class TeamServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class TeamServiceTest extends TestCase
{
    use HelperTrait;
    use TeamServiceTrait;
    use AuthServiceTrait;

    /**
     * Check on create user
     *
     * @return TeamServiceContract
     * @throws Exception
     */
    public function testCreate(): TeamServiceContract
    {
        $this->login();
        $this->teamService__()->create(
            [
                'name' => $this->randString(),
                'description' => $this->randString(),
            ]
        );

        $this->checkDTO($this->teamService__()->dto(), $this->teamService__()->readonly());

        $this->login();

        $user = $this->authService__()->user()->readonly();

        $validated = false;

        try {
            $this->teamService__()->requestService()->create($user, [
                'link' => 'http://zentry.test/teams',
            ]);
        } catch (RuntimeException $exception) {
            $validated = true;
        }

        self::assertTrue($validated);

        $this->teamService__()->requestService()->create($user, [
            'link' => 'http://zentry.test/teams/{id}',
        ]);

        $this->setProtectedProperty($this->authService__(), 'userService__', $this->userService__());

        $this->teamService__()->requestService()->apply();

        self::assertCount(2, $this->teamService__()->readonly()->members());
        $this->teamService__()->leave();
        self::assertCount(1, $this->teamService__()->readonly()->members());

        $this->teamService__()->requestService()->create($user, [
            'link' => 'http://zentry.test/teams/{id}',
        ]);

        self::assertCount(1, $this->teamService__()->readonly()->requests());

        $this->teamService__()->requestService()->reject();
        self::assertCount(0, $this->teamService__()->readonly()->requests());
        self::assertCount(1, $this->teamService__()->readonly()->members());

        $dto = $this->teamService__()->dto();

        $dtoOriginal = $this->teamService__()->workWith($dto->id)->dto();
        static::assertEquals($dto, $dtoOriginal);

        return $this->teamService__();
    }

    /**
     * Check user dto
     *
     * @param TeamDTO              $dto
     * @param TeamReadonlyContract $entity
     *
     * @return void
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    private function checkDTO(TeamDTO $dto, TeamReadonlyContract $entity): void
    {
        static::assertEquals($dto->createdAt, dateTimeFormatted($entity->createdAt()));
        static::assertEquals($dto->updatedAt, dateTimeFormatted($entity->updatedAt()));

        static::assertEquals($dto->name, $entity->name());
        static::assertEquals($dto->description, $entity->description());
        static::assertEquals($dto->members->count(), $entity->members()->count());
        static::assertEquals($dto->requests->count(), $entity->requests()->count());
    }

    /**
     * Check work with method
     *
     * @param TeamServiceContract $service
     *
     * @return TeamServiceContract
     * @throws ExpectationFailedException
     * @throws PropertyNotInit
     * @throws RecursionContextInvalidArgumentException
     * @throws InvalidArgumentException
     * @throws Exception
     * @depends testCreate
     */
    public function testChange(TeamServiceContract $service): TeamServiceContract
    {
        $name = $this->randString();
        $description = $this->randString();

        $service->change(
            [
                'name' => $name,
                'description' => $description,
            ]
        );

        static::assertEquals($service->readonly()->name(), $name);
        static::assertEquals($service->readonly()->description(), $description);

        return $service;
    }

    /**
     * @param TeamServiceContract $service
     *
     * @return TeamServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @depends testChange
     */
    public function testRemove(TeamServiceContract $service): TeamServiceContract
    {
        $team = $service->readonly();

        $service->remove();

        $this->expectException(NotFoundException::class);
        $service->workWith($team->identity());

        return $service;
    }

    /**
     * Check work with method exception
     *
     * @param TeamServiceContract $service
     *
     * @throws BindingResolutionException|NotFoundException
     * @throws InvalidArgumentException
     * @depends testChange
     */
    public function testWorkWithException(TeamServiceContract $service): void
    {
        $this->expectException(NotFoundException::class);
        $service->workWith($this->generateIdentity());
    }
}
