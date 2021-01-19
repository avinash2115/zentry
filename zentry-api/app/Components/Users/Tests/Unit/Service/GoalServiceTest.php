<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Class GoalServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class GoalServiceTest extends TestCase
{
    use HelperTrait;
    use ParticipantServiceTrait;
    use AuthServiceTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->randString(), [], $this->randString()],
            [$this->randString(), [], ''],
            [$this->randString(), [], $this->randString()],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            ['', [], $this->randString()],
        ];
    }

    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     */
    public function testWorkWithException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->participantService__()->goalService()->workWith((string)$this->generateIdentity());
    }

    /**
     * @param string $name
     * @param array  $meta
     * @param string $description
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $name,
        array $meta,
        string $description
    ): void {
        $goalService = $this->participantService__()->goalService();
        $goalService->create(
            [
                'name' => $name,
                'meta' => $meta,
                'description' => $description,
            ]
        );

        self::assertEquals($name, $goalService->readonly()->name());
        self::assertEquals($meta, $goalService->readonly()->meta()->toArray());
        self::assertEquals($description, $goalService->readonly()->description());
        self::assertFalse($goalService->readonly()->isReached());
        self::assertNotNull($goalService->readonly()->createdAt());

        self::assertCount($goalService->listRO()->count(), $this->participantService__()->readonly()->goals());
        self::assertCount($goalService->list()->count(), $this->participantService__()->readonly()->goals());

        $goal = $goalService->readonly();

        $goalService->workWith($goal->identity());

        self::assertTrue($goalService->readonly()->identity()->equals($goal->identity()));
        self::assertTrue($goalService->readonly()->identity()->equals(new Identity($goalService->dto()->id)));

        $description = $this->randString();

        $goalService->change(
            [
                'description' => $description,
            ]
        );

        self::assertEquals($description, $goalService->readonly()->description());

        $goalService->change(
            [
                'reached' => true,
            ]
        );

        self::assertTrue($goalService->readonly()->isReached());

        $goalService->change(
            [
                'reached' => false,
            ]
        );

        self::assertFalse($goalService->readonly()->isReached());
        self::assertEquals($description, $goalService->readonly()->description());

        $name = $this->randString();

        $goalService->change(
            [
                'name' => $name,
            ]
        );

        self::assertEquals($name, $goalService->readonly()->name());
        self::assertNotEquals($goalService->readonly()->description(), $goalService->readonly()->name());

        $goalService->remove();

        self::assertCount($goalService->listRO()->count(), $this->participantService__()->readonly()->goals());
        self::assertCount($goalService->list()->count(), $this->participantService__()->readonly()->goals());
    }

    /**
     * @param string $name
     * @param array  $meta
     * @param string $description
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $name,
        array $meta,
        string $description
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $goalService = $this->participantService__()->goalService();

        $goalService->create(
            [
                'name' => $name,
                'meta' => $meta,
                'description' => $description,
            ]
        );
    }

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->login();

        $user = $this->authService__()->user()->readonly();

        $this->participantService__()->create(
            $user,
            [
                'email' => $this->randEmail(),
                'first_name' => $this->randString(),
                'last_name' => $this->randString(),
                'phone_code' => null,
                'phone_number' => null,
                'avatar' => null,
            ]
        )->readonly();
    }
}
