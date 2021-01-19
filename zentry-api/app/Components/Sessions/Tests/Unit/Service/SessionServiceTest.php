<?php

namespace App\Components\Sessions\Tests\Unit\Service;

use App\Assistants\Files\Services\FileServiceContract;
use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Components\Sessions\Services\Note\NoteServiceContract;
use App\Components\Sessions\Services\Poi\PoiServiceContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Note\NoteDTO;
use App\Components\Sessions\Session\Note\NoteReadonlyContract;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionDTO;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Sessions\Tests\Unit\Traits\SessionHelperTestTrait;
use App\Components\Sessions\ValueObjects\Note\Payload;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Storage;
use Tests\TestCase;
use UnexpectedValueException;

/**
 * Class SessionServiceTest
 *
 * @package App\Components\Sessions\Tests\Unit\Service
 */
class SessionServiceTest extends TestCase
{
    use SessionServiceTrait;
    use AuthServiceTrait;
    use SessionHelperTestTrait;

    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    public function testWorkWithException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->sessionService__()->workWith((string)$this->generateIdentity());
    }

    /**
     * @depends testWorkWithException
     * @throws BindingResolutionException|NotFoundException|NonUniqueResultException|UnexpectedValueException|InvalidArgumentException
     */
    public function testWorkWithActiveException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->sessionService__()->workWithActive();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->randString(), ['lng' => '45.707198', 'lat' => '34.761019', 'place' => $this->randString()], null],
            [$this->randString(5), ['lng' => '45.707198', 'lat' => '34.761019', 'place' => $this->randString()], null],
            [
                '',
                ['lng' => '45.707198', 'lat' => '34.761019', 'place' => $this->randString()],
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * @dataProvider correctDataProvider
     *
     * @param string $name
     * @param array $geo
     * @param null|string $exception
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     * @throws Exception
     */
    public function testSuccessCreationAndProcess(
        string $name,
        array $geo,
        ?string $exception = null
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $dto = $this->sessionService__()->create(
            $user = $this->createUser(),
            [
                'name' => $name,
                'school' => $this->createSchool($this->createTeam($user)),
            ]
        )->dto();

        self::assertCount(1, $this->sessionService__()->list());
        self::assertCount(1, $this->sessionService__()->listRO());

        self::assertInstanceOf(SessionReadonlyContract::class, $this->sessionService__()->readonly());
        self::assertInstanceOf(SessionDTO::class, $this->sessionService__()->dto());
        self::assertEquals(Mutator::TYPE, $dto->_type);

        $newName = $this->randString();

        $this->sessionService__()->workWith($dto->id)->change(
            [
                'name' => $newName,
            ]
        );

        self::assertEquals($newName, $this->sessionService__()->readonly()->name());
        self::assertNull($this->sessionService__()->readonly()->startedAt());
        self::assertNull($this->sessionService__()->readonly()->endedAt());

        $this->sessionService__()->start();

        self::assertNotNull($this->sessionService__()->readonly()->startedAt());

        app()->make(LinkParameters::class)->push($this->sessionService__()->readonly()->identity()->toString());

        $this->checkPoiServiceMethods($this->sessionService__()->poiService());

        $this->checkWrapWithError();

        $this->sessionService__()->end();

        self::assertNotNull($this->sessionService__()->readonly()->endedAt());

        $this->checkWrapWithError();

        collect(StreamReadonlyContract::AVAILABLE_TYPES)->each(
            function (string $type) {
                $this->sessionService__()->streamService()->create(
                    UploadedFile::fake()->create($this->randString()),
                    $type
                );
            }
        );
        $this->sessionService__()->change(
            [
                'geo' => $geo,
            ]
        );

        $this->sessionService__()->wrap();

        $this->checkNotesService();
    }

    /**
     * @param PoiServiceContract $poiService
     *
     * @throws Exception
     */
    private function checkPoiServiceMethods(PoiServiceContract $poiService): void
    {
        collect($this->correctPoiDataProvider())->each(
            function (array $data) use ($poiService) {
                [$type, $startedAt, $endedAt, $name] = $data;

                $poiService->create(
                    [
                        'type' => $type,
                        'name' => $name,
                        'started_at' => dateTimeFormatted($startedAt),
                        'ended_at' => dateTimeFormatted($endedAt),
                    ]
                );

                self::assertInstanceOf(PoiReadonlyContract::class, $poiService->readonly());
                self::assertInstanceOf(PoiDTO::class, $poiService->dto());
                self::assertIsString($poiService->dto()->id);
                self::assertIsString($poiService->dto()->type);

                if ($name === null)  {
                    self::assertNull($poiService->dto()->name);
                } else {
                    self::assertIsString($poiService->dto()->name);
                }

                self::assertIsString($poiService->dto()->startedAt);
                self::assertIsString($poiService->dto()->createdAt);
                self::assertIsString($poiService->dto()->endedAt);
                self::assertTrue($poiService->identity()->equals($poiService->readonly()->identity()));

                self::assertInstanceOf(PoiServiceContract::class, $poiService->workWith($poiService->identity()));
                self::assertCount(1, $poiService->list());
                $poiService->remove();
                self::assertCount(0, $poiService->list());
            }
        );
    }

    /**
     * @throws BindingResolutionException|NoResultException|NonUniqueResultException|PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->login();
    }

    /**
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    private function checkWrapWithError(): void
    {
        try {
            $this->sessionService__()->wrap();
        } catch (RuntimeException $exception) {
            self::assertTrue(true);
        }
    }

    /**
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PHPUnitException
     * @throws PropertyNotInit
     * @throws RecursionContextInvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws ReflectionException
     * @throws Exception
     */
    private function checkNotesService(): void
    {
        Storage::fake('tests');
        $file = UploadedFile::fake()->image($this->randString() . '.ext');

        $payload = new Payload($this->randString());
        $this->sessionService__()->noteService()->create($payload);
        self::assertNull($this->sessionService__()->noteService()->readonly()->url());
        $this->sessionService__()->noteService()->remove();

        $this->setProtectedProperty(
            $this->sessionService__()->noteService(),
            'fileService__',
            app()->make(
                FileServiceContract::class,
                [
                    'storage' => Storage::fake('tests'),
                ]
            )
        );

        $this->sessionService__()->noteService()->create($payload, $file);

        self::assertInstanceOf(NoteReadonlyContract::class, $this->sessionService__()->noteService()->readonly());
        self::assertInstanceOf(NoteDTO::class, $this->sessionService__()->noteService()->dto());
        self::assertNotNull($this->sessionService__()->noteService()->readonly()->url());

        self::assertIsString($this->sessionService__()->noteService()->dto()->id);
        self::assertIsString($this->sessionService__()->noteService()->dto()->text);
        self::assertNotEmpty($this->sessionService__()->noteService()->dto()->text);
        self::assertNotEmpty($this->sessionService__()->noteService()->dto()->url);
        self::assertIsString($this->sessionService__()->noteService()->dto()->createdAt);
        self::assertIsString($this->sessionService__()->noteService()->dto()->updatedAt);

        self::assertTrue(
            $this->sessionService__()->noteService()->identity()->equals(
                $this->sessionService__()->noteService()->readonly()->identity()
            )
        );

        self::assertInstanceOf(
            NoteServiceContract::class,
            $this->sessionService__()->noteService()->workWith(
                $this->sessionService__()->noteService()->identity()
            )
        );
        self::assertCount(1, $this->sessionService__()->noteService()->list());
        $this->sessionService__()->noteService()->remove();
        self::assertCount(0, $this->sessionService__()->noteService()->list());
    }
}
