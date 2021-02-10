<?php

namespace App\Components\Sessions\Tests\Unit\Integration;

use App\Assistants\Files\ValueObjects\File;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Session\Transcription\Repository\TranscriptionRepositoryODM;
use App\Components\Sessions\Session\Transcription\TranscriptionContract;
use App\Components\Sessions\Session\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Repository\SessionRepositoryDoctrine;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\Stream\StreamContract;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Users\Team\Mutators\DTO\Mutator as TeamMutator;
use App\Components\Users\Team\School\Mutators\DTO\Mutator as SchoolMutator;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tests\IntegrationTestCase;
use UnexpectedValueException;

/**
 * Class TranscriptionIntegrationTest
 */
class TranscriptionIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;

    /**
     * @var SessionRepositoryDoctrine|null
     */
    private ?SessionRepositoryDoctrine $repository = null;

    /**
     * @throws Exception
     */
    public function testSuccessCreation(): void
    {
        $sessionName = $this->randString();

        $response = $this->withAuthHeader()->json(
            'POST',
            '/teams',
            $this->asData(
                TeamMutator::TYPE,
                [
                    'name' => $this->randString(),
                    'description' => $this->randString(),
                ]
            )
        );

        $response->assertStatus(201);

        $team = $this->asJsonApi($response);

        $response = $this->withAuthHeader()->json(
            'POST',
            route('users.teams.schools.create', [$team->id()]),
            $this->asData(
                SchoolMutator::TYPE,
                [
                    'name' => $this->randString(),
                ]
            )
        );

        $response->assertStatus(201);

        $school = $this->asJsonApi($response);

        $response = $this->withAuthHeader()->json(
            'POST',
            '/sessions/start',
            $this->asData(
                Mutator::TYPE,
                [
                    'name' => $sessionName,
                ],
                [
                    'team' => $this->asData(
                        TeamMutator::class,
                        [],
                        [],
                        $team->id(),
                    ),
                    'school' => $this->asData(
                        SchoolMutator::class,
                        [],
                        [],
                        $school->id(),
                    ),
                ]
            )
        );

        $response->assertStatus(200);

        $jsonApiStarted = $this->asJsonApi($response);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.pois.create', $jsonApiStarted->id()),
            $this->asData(
                \App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator::TYPE,
                [
                    'type' => PoiReadonlyContract::POI_TYPE,
                    'started_at' => dateTimeFormatted(new DateTime('2016-01-01 12:00:00')),
                    'ended_at' => dateTimeFormatted(new DateTime('2016-01-01 12:00:10')),
                ]
            )
        );

        $response = $this->withAuthHeader()->json(
            'POST',
            route('sessions.end', [$jsonApiStarted->id()]),
            $this->asData('')
        );

        $response->assertStatus(200);

        $this->mockStreams($jsonApiStarted);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.wrap', [$jsonApiStarted->id()]),
            $this->asData('')
        )->assertStatus(200);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('sessions.index'),
            $this->asData('')
        );

        $response->assertStatus(200);

        $jsonAPIList = $this->asJsonApi($response);

        self::assertTrue($jsonAPIList->asJsonApiCollection()->isNotEmpty());

        $jsonAPI = $jsonAPIList->asJsonApiCollection()->first();

        if (!$jsonAPI instanceof JsonApi) {
            throw new UnexpectedValueException();
        }

        $jsonAPIList = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                route('sessions.pois.index', [$jsonAPI->id()]),
                $this->asData('')
            )
        );

        $poi = $jsonAPIList->asJsonApiCollection()->first();

        if (!$poi instanceof JsonApi) {
            throw new UnexpectedValueException();
        }

        $this->json(
            'POST',
            route('sessions.transcription.failed', [$jsonAPI->id(), $poi->id()]),
            $this->asData(
                '',
                [
                    'message' => $this->randString(),
                ]
            )
        )->assertStatus(200);

        $this->json(
            'POST',
            route('sessions.transcription.create', [$jsonAPI->id(), $poi->id()]),
            [
                'data' => [
                    [
                        'attributes' => [
                            'word' => $this->randString(),
                            'started_at' => 0,
                            'ended_at' => 1,
                            'speaker_tag' => 1,
                        ],
                    ],
                    [
                        'attributes' => [
                            'word' => $this->randString(),
                            'started_at' => 0,
                            'ended_at' => 5,
                            'speaker_tag' => 5,
                        ],
                    ],
                ],
            ]
        )->assertStatus(200);

        $this->json(
            'POST',
            route('sessions.transcription.create', [$jsonAPI->id(), IdentityGenerator::next()->toString()]),
            [
                'data' => [
                    [
                        'attributes' => [
                            'session_id' => $jsonAPI->id(),
                            'word' => $this->randString(),
                            'started_at' => 0,
                            'ended_at' => 2,
                            'speaker_tag' => 5,
                        ],
                    ],
                ],
            ]
        )->assertStatus(404);

        $this->json(
            'POST',
            route('sessions.transcription.create', [$jsonAPI->id(), $poi->id()]),
            [
                'data' => [
                    [
                        'attributes' => [
                            'word' => $this->randString(),
                            'started_at' => 2,
                            'ended_at' => 0,
                            'speaker_tag' => 5,
                        ],
                    ],
                ],
            ]
        )->assertStatus(500);

        $transcriptionRepository = $this->app->make(TranscriptionRepositoryODM::class);

        $transcriptionRepository->getAll()->each(
            static function (TranscriptionContract $transcription) use ($transcriptionRepository) {
                $transcriptionRepository->destroy($transcription);
            }
        );
    }

    /**
     * @param JsonApi $sessionJsonApi
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws InvalidArgumentException
     */
    private function mockStreams(JsonApi $sessionJsonApi): void
    {
        $session = $this->sessionRepository()->byIdentity(new Identity($sessionJsonApi->id()));

        collect(StreamReadonlyContract::AVAILABLE_TYPES)->each(
            function (string $type) use ($session) {
                $session->addStream($this->stream($session, $type));
            }
        );

        $this->flush();
    }

    /**
     * @return SessionRepositoryDoctrine
     * @throws BindingResolutionException
     */
    private function sessionRepository(): SessionRepositoryDoctrine
    {
        if (!$this->repository instanceof SessionRepositoryDoctrine) {
            $this->repository = app()->make(SessionRepositoryDoctrine::class);
        }

        return $this->repository;
    }

    /**
     * @param SessionContract $session
     * @param string          $type
     *
     * @return StreamContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function stream(SessionContract $session, string $type): StreamContract
    {
        return app()->make(
            StreamContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'session' => $session,
                'type' => $type,
                'file' => new File($this->randString(), $this->randString()),
            ]
        );
    }
}
