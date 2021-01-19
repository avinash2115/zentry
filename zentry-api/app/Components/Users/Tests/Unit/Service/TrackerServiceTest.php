<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Participant\Goal\GoalServiceContract;
use App\Components\Users\Services\Participant\Goal\Tracker\TrackerServiceContract;
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
 * Class TrackerServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class TrackerServiceTest extends TestCase
{
    use HelperTrait;
    use ParticipantServiceTrait;
    use AuthServiceTrait;

    /**
     * @var GoalServiceContract
     */
    private GoalServiceContract $goalService;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->randString(), $this->randString()],
            [$this->randString(), $this->randString()],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            ['', $this->randString()],
            [$this->randString(), ''],
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
     * @param string $icon
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws Exception
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $name,
        string $icon
    ): void {
        $trackerService = $this->goalService->trackerService();

        self::assertCount(0, $this->goalService->readonly()->trackers());

        $trackerService->createDefault();

        self::assertCount(count(TrackerServiceContract::DEFAULT_TRACKERS), $this->goalService->readonly()->trackers());

        collect(TrackerServiceContract::DEFAULT_TRACKERS)->each(
            function (string $icon, string $name) {
                $tracker = $this->goalService->readonly()->trackers()->first(
                    static function (TrackerReadonlyContract $tracker) use ($name) {
                        return $tracker->name() === $name;
                    }
                );
                self::assertInstanceOf(TrackerReadonlyContract::class, $tracker);
            }
        );

        $trackerService->create(
            [
                'name' => $name,
                'icon' => $icon,
            ]
        );

        self::assertEquals($name, $trackerService->readonly()->name());
        self::assertEquals($icon, $trackerService->readonly()->icon());
        self::assertNotNull($trackerService->readonly()->createdAt());

        self::assertCount($trackerService->listRO()->count(), $this->goalService->readonly()->trackers());
        self::assertCount($trackerService->list()->count(), $this->goalService->readonly()->trackers());

        $tracker = $trackerService->readonly();

        $trackerService->workWith($tracker->identity());

        self::assertTrue($trackerService->readonly()->identity()->equals($tracker->identity()));
        self::assertTrue($trackerService->readonly()->identity()->equals(new Identity($trackerService->dto()->id)));

        $name = $this->randString();

        $trackerService->change(
            [
                'name' => $name,
            ]
        );

        self::assertEquals($name, $trackerService->readonly()->name());

        $icon = $this->randString();

        $trackerService->change(
            [
                'icon' => $icon,
            ]
        );

        self::assertEquals($name, $trackerService->readonly()->name());
        self::assertNotEquals($trackerService->readonly()->icon(), $trackerService->readonly()->name());

        $exceptionThrown = false;
        try {
            $trackerService->create(
                [
                    'name' => $this->randString(),
                    'icon' => $icon,
                ]
            );
            $trackerService->change(
                [
                    'name' => $name,
                ]
            );
        } catch (InvalidArgumentException $exception) {
            $trackerService->remove();
            $exceptionThrown = true;
        }

        self::assertTrue($exceptionThrown);

        $trackerService->workWith($tracker->identity());

        $trackerService->remove();

        self::assertCount($trackerService->listRO()->count(), $this->goalService->readonly()->trackers());
        self::assertCount($trackerService->list()->count(), $this->goalService->readonly()->trackers());
    }

    /**
     * @param string $name
     * @param string $icon
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $name,
        string $icon
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $this->goalService->trackerService()->create(
            [
                'name' => $name,
                'icon' => $icon,
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

        $this->goalService = $this->participantService__()->create(
            $user,
            [
                'email' => $this->randEmail(),
                'first_name' => $this->randString(),
                'last_name' => $this->randString(),
                'phone_code' => null,
                'phone_number' => null,
                'avatar' => null,
            ]
        )->goalService()->create(
            [
                'name' => $this->randString(),
                'meta' => [],
                'description' => $this->randString(),
            ]
        );
    }
}
