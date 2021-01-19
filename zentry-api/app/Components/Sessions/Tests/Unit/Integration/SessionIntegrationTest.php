<?php

namespace App\Components\Sessions\Tests\Unit\Integration;

use App\Assistants\Files\ValueObjects\File;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Services\Service\Mutators\DTO\Mutator as ServiceMutator;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Repository\SessionRepositoryDoctrine;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\SessionDTO;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Session\Stream\StreamContract;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Users\Participant\Mutators\DTO\Mutator as ParticipantMutator;
use App\Components\Users\Team\Mutators\DTO\Mutator as TeamMutator;
use App\Components\Users\Team\School\Mutators\DTO\Mutator as SchoolMutator;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use App\Http\Middleware\Access\Device\Authenticate;
use DateInterval;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\IntegrationTestCase;
use UnexpectedValueException;
use Arr;

/**
 * Class SessionIntegrationTest
 */
class SessionIntegrationTest extends IntegrationTestCase
{
    use HelperTrait;

    /**
     * @var SessionRepositoryDoctrine|null
     */
    private ?SessionRepositoryDoctrine $repository = null;

    /**
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function testScheduled(): void
    {
        $this->withAuthHeader()->json(
            'POST',
            '/sessions/schedule',
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                [
                    'name' => $this->randString(),
                ]
            )
        )->assertStatus(500);

        $this->withAuthHeader()->json(
            'POST',
            '/sessions/schedule',
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                [
                    'name' => $this->randString(),
                    'scheduled_to' => dateTimeFormatted(new DateTime()),
                ]
            )
        )->assertStatus(500);

        $this->withAuthHeader()->json(
            'POST',
            '/sessions/schedule',
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                [
                    'name' => $this->randString(),
                    'scheduled_on' => dateTimeFormatted(new DateTime()),
                    'scheduled_to' => dateTimeFormatted((new DateTime())->sub(new DateInterval('P1D'))),
                ]
            )
        )->assertStatus(500);

        $serviceData = $this->createService();
        $schoolData = $this->createTeamAndSchool();

        $scheduledSession = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                '/sessions/schedule',
                $this->asData(
                    \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                    [
                        'name' => $this->randString(),
                        'type' => SessionReadonlyContract::TYPE_DEFAULT,
                        'description' => $this->randString(),
                        'scheduled_on' => dateTimeFormatted((new DateTime())->add(new DateInterval('P1D'))),
                        'scheduled_to' => dateTimeFormatted((new DateTime())->add(new DateInterval('P2D'))),
                    ],
                    [
                        'service' => $this->asData(
                            ServiceMutator::class,
                            [],
                            [],
                            $serviceData->id(),
                        ),
                        'team' => $this->asData(
                            TeamMutator::class,
                            [],
                            [],
                            $schoolData['team_id'],
                        ),
                        'school' => $this->asData(
                            SchoolMutator::class,
                            [],
                            [],
                            $schoolData['school_id'],
                        ),
                    ]
                )
            )->assertStatus(200)
        );

        $response = $this->withAuthHeader()->json(
            'GET',
            '/sessions/active',
        );
        $response->assertStatus(404);

        $sessions = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                '/sessions/scheduled',
            )->assertStatus(200)
        );

        self::assertCount(1, $sessions->asJsonApiCollection());
        $listedSession = $sessions->asJsonApiCollection()->first();

        if (!$listedSession instanceof JsonApi) {
            throw new Exception();
        }

        self::assertEquals($scheduledSession->id(), $listedSession->id());

        $scheduledSession = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                route('sessions.start', [$scheduledSession->id()]),
                $this->asData(
                    \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                )
            )->assertStatus(200)
        );

        $response = $this->withAuthHeader()->json(
            'POST',
            route('sessions.end', [$scheduledSession->id()]),
            $this->asData('', [])
        );

        $response->assertStatus(200);

        //Uncomment with rewrited json method
        $this->mockStreams($scheduledSession);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.wrap', [$scheduledSession->id()]),
            $this->asData('')
        )->assertStatus(200);

        $session = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                route('sessions.index'),
            )
        )->asJsonApiCollection()->first();

        if (!$session instanceof JsonApi) {
            throw new Exception();
        }

        self::assertEquals($scheduledSession->attributes()->get('name'), $session->attributes()->get('name'));
        self::assertEquals(
            $scheduledSession->attributes()->get('description'),
            $session->attributes()->get('description')
        );
        self::assertEquals($scheduledSession->attributes()->get('type'), $session->attributes()->get('type'));
    }

    /**
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws RecursionContextInvalidArgumentException
     */
    public function testFullFlow(): void
    {
        $sessionName = $this->randString();

        $serviceData = $this->createService();
        $schoolData = $this->createTeamAndSchool();

        $created = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                '/sessions/start',
                $this->asData(
                    \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                    [
                        'name' => $sessionName,
                    ],
                    [
                        'service' => $this->asData(
                            ServiceMutator::class,
                            [],
                            [],
                            $serviceData->id(),
                        ),
                        'team' => $this->asData(
                            TeamMutator::class,
                            [],
                            [],
                            $schoolData['team_id'],
                        ),
                        'school' => $this->asData(
                            SchoolMutator::class,
                            [],
                            [],
                            $schoolData['school_id'],
                        ),
                    ]
                )
            )->assertStatus(200)
        );

        $response = $this->withAuthHeader()->json(
            'POST',
            '/sessions/start',
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                [
                    'name' => $sessionName,
                ],
                [
                    'team' => $this->asData(
                        TeamMutator::class,
                        [],
                        [],
                        $schoolData['team_id'],
                    ),
                    'school' => $this->asData(
                        SchoolMutator::class,
                        [],
                        [],
                        $schoolData['school_id'],
                    ),
                ]
            )
        );

        $response->assertStatus(424);

        self::assertEquals(
            'Another active session existed. You should end it, before start the new one.',
            Arr::get(json_decode($response->original, true), 'data.title')
        );

        $response = $this->withAuthHeader()->json(
            'GET',
            '/sessions/active',
        );
        $response->assertStatus(200);

        self::assertSame($this->asJsonApi($response)->id(), $created->id());

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.end', [$created->id()]),
            $this->asData('')
        )->assertStatus(200);

        $response = $this->withAuthHeader()->json(
            'POST',
            '/sessions/start',
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                [
                    'name' => $sessionName,
                ],
                [
                    'team' => $this->asData(
                        TeamMutator::class,
                        [],
                        [],
                        $schoolData['team_id'],
                    ),
                    'school' => $this->asData(
                        SchoolMutator::class,
                        [],
                        [],
                        $schoolData['school_id'],
                    ),
                ]
            )
        );

        $response->assertStatus(200);
    }

    /**
     * @throws Exception
     */
    public function testSuccessCreation(): void
    {
        $sessionName = $this->randString();
        $serviceData = $this->createService();
        $schoolData = $this->createTeamAndSchool();

        $response = $this->withAuthHeader()->json(
            'POST',
            '/sessions/start',
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                [
                    'name' => $sessionName,
                ],
                [
                    'service' => $this->asData(
                        ServiceMutator::class,
                        [],
                        [],
                        $serviceData->id(),
                    ),
                    'team' => $this->asData(
                        TeamMutator::class,
                        [],
                        [],
                        $schoolData['team_id'],
                    ),
                    'school' => $this->asData(
                        SchoolMutator::class,
                        [],
                        [],
                        $schoolData['school_id'],
                    ),
                ]
            )
        );

        $response->assertStatus(200);

        $jsonApiStarted = $this->asJsonApi($response);

        $response = $this->withAuthHeader()->json(
            'GET',
            '/sessions/active',
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                [
                    'name' => $sessionName,
                ]
            )
        );

        $response->assertStatus(200);

        $sessionJsonApi = $this->asJsonApi($response);
        self::assertEquals($sessionJsonApi->attributes()->toArray(), $jsonApiStarted->attributes()->toArray());

        $connectingPayload = new ConnectingPayload(
            $this->randString(), $this->randString(), $this->randString()
        );

        $this->connectDeviceAndRemove($sessionJsonApi, $connectingPayload);

        $this->connectDevice($sessionJsonApi, $connectingPayload);

        $this->createPois($sessionJsonApi, $connectingPayload);

        $this->createProgress($sessionJsonApi, $connectingPayload);

        $response = $this->withAuthHeader()->json(
            'POST',
            route('sessions.end', [$sessionJsonApi->id()]),
            $this->asData('', [])
        );

        $response->assertStatus(200);

        //Uncomment with rewrited json method
        $this->mockStreams($sessionJsonApi);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.change', [$sessionJsonApi->id()]),
            $this->asData(
                '',
                [
                    'geo' => $this->randString(),
                ]
            )
        )->assertStatus(500);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.change', [$sessionJsonApi->id()]),
            $this->asData(
                '',
                [
                    'geo' => [
                        'lng' => 45.707198,
                        'lat' => '45.707198',
                        'place' => '',
                    ],
                ]
            )
        )->assertStatus(500);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.change', [$sessionJsonApi->id()]),
            $this->asData(
                '',
                [
                    'geo' => null,
                ]
            )
        )->assertStatus(200);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.change', [$sessionJsonApi->id()]),
            $this->asData(
                '',
                [
                    'geo' => [
                        'lng' => 45.707198,
                        'lat' => '45.707198',
                        'place' => $this->randString(),
                    ],
                ]
            )
        )->assertStatus(200);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.wrap', [$sessionJsonApi->id()]),
            $this->asData('')
        )->assertStatus(200);

        $this->saveDevice($sessionJsonApi, $connectingPayload);

        $this->checkSession();
    }

    /**
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws UnexpectedValueException
     * @throws Exception
     */
    private function checkSession(): void
    {
        $response = $this->withAuthHeader()->json(
            'GET',
            route('sessions.index'),
            $this->asData('', [])
        );

        $response->assertStatus(200);

        $jsonAPIList = $this->asJsonApi($response);

        self::assertCount(1, $jsonAPIList->asJsonApiCollection());

        self::assertTrue($jsonAPIList->asJsonApiCollection()->isNotEmpty());

        $response = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                route('sessions.index') . '?filter[statuses][collection][]=' . SessionReadonlyContract::STATUS_WRAPPED,
                $this->asData('', [])
            )->assertStatus(200)
        );

        self::assertCount(1, $response->asJsonApiCollection());

        $response = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                route('sessions.index') . '?filter[statuses][collection][]=' . SessionReadonlyContract::STATUS_STARTED,
                $this->asData('', [])
            )->assertStatus(200)
        );

        self::assertCount(0, $response->asJsonApiCollection());

        $response = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                route('sessions.index') . '?filter[statuses][collection][]=' . SessionReadonlyContract::STATUS_ENDED,
                $this->asData('', [])
            )->assertStatus(200)
        );

        self::assertCount(0, $response->asJsonApiCollection());

        $response = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                route('sessions.index') . '?filter[statuses][collection][]=' . SessionReadonlyContract::STATUS_NEW,
                $this->asData('', [])
            )->assertStatus(200)
        );

        self::assertCount(0, $response->asJsonApiCollection());

        $jsonAPI = $jsonAPIList->asJsonApiCollection()->first();

        if (!$jsonAPI instanceof JsonApi) {
            throw new UnexpectedValueException();
        }

        $this->withAuthHeader()->json(
            'GET',
            route(SessionDTO::ROUTE_NAME_SHOW, $jsonAPI->id()),
            $this->asData('', [])
        )->assertStatus(200);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.change', $jsonAPI->id()),
            $this->asData(
                '',
                [
                    'geo' => [
                        'lng' => 45.707198,
                        'lat' => '45.707198',
                        'place' => '',
                    ],
                ]
            )
        )->assertStatus(500);

        $geo = [
            'lng' => 45.707198,
            'lat' => '45.707198',
            'place' => $this->randString(),
        ];

        $tags = [
            [
                'tag' => $this->randString(),
            ],
            [
                'tag' => $this->randString(),
            ],
        ];

        $name = $this->randString();

        $r = $this->withAuthHeader()->json(
            'POST',
            route(SessionDTO::ROUTE_NAME_SHOW, $jsonAPI->id()),
            $this->asData(
                '',
                [
                    'geo' => null,
                ]
            )
        );

        $response = $this->withAuthHeader()->json(
            'POST',
            route(SessionDTO::ROUTE_NAME_SHOW, $jsonAPI->id()),
            $this->asData(
                '',
                [
                    'geo' => $geo,
                    'tags' => $tags,
                    'name' => $name,
                ]
            )
        )->assertStatus(200);

        $session = $this->asJsonApi($response);

        self::assertEquals($geo, $session->attributes()->get('geo'));
        self::assertEquals($tags, $session->attributes()->get('tags'));
        self::assertEquals($name, $session->attributes()->get('name'));

        $this->checkSessionPois($session);

        $this->checkSessionNotes($session);

        $progressList = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                route('sessions.progress.index', $jsonAPI->id()),
            )->assertStatus(200)
        );

        self:: assertNotEquals(0, $progressList->asJsonApiCollection());

        self::assertTrue(
            $progressList->asJsonApiCollection()->some(
                function (JsonApi $jsonApi) {
                    return $jsonApi->relation('poi');
                }
            )
        );
    }

    /**
     * @param JsonApi $input
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws NonUniqueResultException
     * @throws UnexpectedValueException
     * @throws RecursionContextInvalidArgumentException
     * @throws Exception
     */
    private function checkSessionPois(JsonApi $input): void
    {
        $response = $this->withAuthHeader()->json(
            'GET',
            route('sessions.pois.index', $input->id()),
            $this->asData('')
        );

        $response->assertStatus(200);

        $jsonAPIList = $this->asJsonApi($response);

        self::assertTrue($jsonAPIList->asJsonApiCollection()->isNotEmpty());

        $jsonAPI = $jsonAPIList->asJsonApiCollection()->first();

        if (!$jsonAPI instanceof JsonApi) {
            throw new UnexpectedValueException();
        }

        $this->withAuthHeader()->json(
            'GET',
            route(PoiDTO::ROUTE_NAME_SHOW, [$input->id(), $jsonAPI->id()]),
            $this->asData('', [])
        )->assertStatus(200);

        $tags = [
            [
                'tag' => $this->randString(),
            ],
            [
                'tag' => $this->randString(),
            ],
        ];

        $response = $this->withAuthHeader()->json(
            'POST',
            route('sessions.pois.change', [$input->id(), $jsonAPI->id()]),
            $this->asData(
                '',
                [
                    'tags' => $tags,
                ]
            )
        )->assertStatus(200);

        $session = $this->asJsonApi($response);

        self::assertEquals($tags, $session->attributes()->get('tags'));

        $this->withAuthHeader()->withHeaders(
            [
                'X-HTTP-METHOD-OVERRIDE' => 'DELETE',
            ]
        )->json(
            'POST',
            route('sessions.pois.remove', [$input->id(), $jsonAPI->id()]),
            $this->asData('')
        );
    }

    /**
     * @param JsonApi $input
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws NonUniqueResultException
     * @throws RecursionContextInvalidArgumentException
     * @throws UnexpectedValueException
     * @throws Exception
     */
    private function checkSessionNotes(JsonApi $input): void
    {
        $this->withAuthHeader()->json(
            'POST',
            route('sessions.notes.create', [$input->id()]),
            $this->asData(
                '',
                [
                    'text' => $this->randString(),
                ]
            )
        )->assertStatus(201);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('sessions.notes.index', $input->id()),
            $this->asData('')
        );

        $note = $this->asJsonApi($response)->asJsonApiCollection()->first();

        if (!$note instanceof JsonApi) {
            throw new UnexpectedValueException();
        }

        $this->withAuthHeader()->json(
            'GET',
            route('sessions.notes.show', [$input->id(), $note->id()]),
        )->assertStatus(200);

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.notes.change', [$input->id(), $note->id()]),
            $this->asData(
                '',
                [
                    'text' => $this->randString(),
                ]
            )
        )->assertStatus(200);

        $this->withAuthHeader()->withDeleteHeader()->json(
            'POST',
            route('sessions.notes.remove', [$input->id(), $note->id()]),
            $this->asData(
                '',
                []
            )
        )->assertStatus(200);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('sessions.pois.index', $input->id()),
            $this->asData('')
        );

        $response->assertStatus(200);

        $jsonAPIList = $this->asJsonApi($response);

        self::assertTrue($jsonAPIList->asJsonApiCollection()->isNotEmpty());

        $jsonAPI = $jsonAPIList->asJsonApiCollection()->first();

        if (!$jsonAPI instanceof JsonApi) {
            throw new UnexpectedValueException();
        }

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.notes.create', [$input->id()]),
            $this->asData(
                '',
                [
                    'text' => $this->randString(),
                ],
                [
                    'poi' => $this->asData(
                        \App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator::TYPE,
                        [],
                        [],
                        $jsonAPI->id()
                    ),
                ]
            )
        )->assertStatus(201);

        $response = $this->withAuthHeader()->json(
            'GET',
            route('sessions.notes.index', $input->id()),
            $this->asData('')
        );

        self::assertCount(1, $this->asJsonApi($response)->asJsonApiCollection());
    }

    /**
     * @param JsonApi           $sessionJsonApi
     * @param ConnectingPayload $connectingPayload
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    private function saveDevice(JsonApi $sessionJsonApi, ConnectingPayload $connectingPayload): void
    {
        $this->json(
            'POST',
            route('sessions.devices.save', [$sessionJsonApi->id()]),
            $this->asData('', [])
        )->assertStatus(401);

        $response = $this->withHeaders(
            [
                Authenticate::HEADER => $connectingPayload->reference(),
            ]
        )->json(
            'POST',
            route('sessions.devices.save', [$sessionJsonApi->id()]),
            $this->asData('', [])
        );

        $response->assertStatus(200)->assertJson(['acknowledge' => true]);

        $response = $this->withAuthHeader()->json(
            'GET',
            '/devices'
        );

        $response->assertStatus(200);

        self::assertNotEmpty($response->content());
    }

    /**
     * @param JsonApi           $sessionJsonApi
     * @param ConnectingPayload $connectingPayload
     *
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    private function connectDevice(JsonApi $sessionJsonApi, ConnectingPayload $connectingPayload): void
    {
        $response = $this->json(
            'POST',
            route(SessionServiceContract::ROUTE_CONNECT_DEVICE, [$sessionJsonApi->id()]),
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                $connectingPayload->attributes()->toArray()
            )
        );

        $response->assertStatus(200);

        $jsonApi = $this->asJsonApi($response);

        self::assertEquals($jsonApi->attributes()->get('model'), $connectingPayload->model());
        self::assertEquals($jsonApi->attributes()->get('type'), $connectingPayload->deviceType());
        self::assertEquals($jsonApi->attributes()->get('reference'), $connectingPayload->reference());

        $response = $this->withHeaders(
            [
                Authenticate::HEADER => $connectingPayload->reference(),
            ]
        )->json(
            'GET',
            '/users/current',
        );

        $response->assertStatus(200);
    }

    /**
     * @param JsonApi           $sessionJsonApi
     * @param ConnectingPayload $connectingPayload
     *
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    private function connectDeviceAndRemove(JsonApi $sessionJsonApi, ConnectingPayload $connectingPayload): void
    {
        $response = $this->json(
            'POST',
            route(SessionServiceContract::ROUTE_CONNECT_DEVICE, [$sessionJsonApi->id()]),
            $this->asData(
                \App\Components\Sessions\Session\Mutators\DTO\Mutator::TYPE,
                $connectingPayload->attributes()->toArray()
            )
        );

        $response->assertStatus(200);

        $jsonApi = $this->asJsonApi($response);

        self::assertEquals($jsonApi->attributes()->get('model'), $connectingPayload->model());
        self::assertEquals($jsonApi->attributes()->get('type'), $connectingPayload->deviceType());
        self::assertEquals($jsonApi->attributes()->get('reference'), $connectingPayload->reference());

        $response = $this->withHeaders(
            [
                Authenticate::HEADER => $connectingPayload->reference(),
            ]
        )->json(
            'GET',
            '/users/current',
        );
        $response->assertStatus(200);

        $this->withDeleteHeader()->withHeaders(
            [
                Authenticate::HEADER => $connectingPayload->reference(),
            ]
        )->json(
            'POST',
            route('sessions.devices.disconnect', [$sessionJsonApi->id()]),
            $this->asData(
                ''
            )
        )->assertStatus(200);

        $this->withHeaders(
            [
                Authenticate::HEADER => $connectingPayload->reference(),
            ]
        )->json(
            'GET',
            '/users/current',
        )->assertStatus(401);
    }

    /**
     * @param JsonApi           $sessionJsonApi
     * @param ConnectingPayload $connectingPayload
     * @param JsonApi|null      $poi
     *
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     */
    private function createProgress(
        JsonApi $sessionJsonApi,
        ConnectingPayload $connectingPayload,
        ?JsonApi $poi = null
    ): void {
        $response = $this->withAuthHeader()->json(
            'POST',
            '/participants',
            $this->asData(
                ParticipantMutator::TYPE,
                [
                    'email' => $this->randEmail(),
                    'firstName' => $this->randString(),
                    'lastName' => $this->randString(),
                ]
            )
        )->assertStatus(201);

        $participant = $this->asJsonApi($response);

        $goal = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                route('users.participants.goals.create', [$participant->id()]),
                $this->asData(
                    \App\Components\Users\Participant\Goal\Mutators\DTO\Mutator::TYPE,
                    [
                        'name' => $this->randString(),
                    ]
                )
            )->assertStatus(201)
        );

        $tracker = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                route('users.participants.goals.trackers.create', [$participant->id(), $goal->id()]),
                $this->asData(
                    \App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator::TYPE,
                    [
                        'name' => $this->randString(),
                        'icon' => $this->randString(),
                    ]
                )
            )->assertStatus(201)
        );

        $relationships = [
            'participant' => $this->asData(ParticipantMutator::TYPE, [], [], $participant->id()),
            'goal' => $this->asData(
                \App\Components\Users\Participant\Goal\Mutators\DTO\Mutator::TYPE,
                [],
                [],
                $goal->id()
            ),
            'tracker' => $this->asData(
                \App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator::TYPE,
                [],
                [],
                $tracker->id()
            ),
        ];

        if ($poi instanceof JsonApi) {
            $relationships['poi'] = $this->asData(
                \App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator::TYPE,
                [],
                [],
                $poi->id()
            );
        }

        $this->withAuthHeader()->json(
            'POST',
            route('sessions.progress.create', [$sessionJsonApi->id()]),
            $this->asData(
                \App\Components\Sessions\Session\Progress\Mutators\DTO\Mutator::TYPE,
                [
                    'datetime' => $this->randString(),
                ],
                $relationships
            )
        )->assertStatus(500);

        $this->flush();

        $progress = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'POST',
                route('sessions.progress.create', [$sessionJsonApi->id()]),
                $this->asData(
                    \App\Components\Sessions\Session\Progress\Mutators\DTO\Mutator::TYPE,
                    [
                        'datetime' => dateTimeFormatted(new DateTime()),
                    ],
                    $relationships
                )
            )->assertStatus(201)
        );

        $progressDirect = $this->asJsonApi(
            $this->withAuthHeader()->json(
                'GET',
                route('sessions.progress.show', [$sessionJsonApi->id(), $progress->id()]),
            )->assertStatus(200)
        );

        self::assertEquals($progressDirect->id(), $progress->id());
    }

    /**
     * @param JsonApi           $sessionJsonApi
     * @param ConnectingPayload $connectingPayload
     *
     * @throws NonUniqueResultException
     * @throws BindingResolutionException
     * @throws PHPUnitException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    private function createPois(JsonApi $sessionJsonApi, ConnectingPayload $connectingPayload): void
    {
        $this->withHeaders(
            [
                Authenticate::HEADER => $connectingPayload->reference(),
            ]
        )->json(
            'POST',
            route('sessions.pois.create', $sessionJsonApi->id()),
            $this->asData(
                Mutator::TYPE,
                []
            )
        )->assertStatus(500);

        $this->withHeaders(
            [
                Authenticate::HEADER => $connectingPayload->reference(),
            ]
        )->json(
            'POST',
            route('sessions.pois.create', IdentityGenerator::next()->toString()),
            $this->asData(
                Mutator::TYPE,
                [
                    'type' => PoiReadonlyContract::POI_TYPE,
                    'started_at' => dateTimeFormatted(new DateTime('2016-01-01 12:00:00')),
                    'ended_at' => dateTimeFormatted(new DateTime('2016-01-01 12:00:10')),
                ]
            )
        )->assertStatus(404);

        $response = $this->withHeaders(
            [
                Authenticate::HEADER => $connectingPayload->reference(),
            ]
        )->json(
            'POST',
            route('sessions.pois.create', $sessionJsonApi->id()),
            $this->asData(
                Mutator::TYPE,
                [
                    'type' => PoiReadonlyContract::POI_TYPE,
                    'started_at' => dateTimeFormatted(new DateTime('2016-01-01 12:00:00')),
                    'ended_at' => dateTimeFormatted(new DateTime('2016-01-01 12:00:10')),
                ]
            )
        );

        $response->assertStatus(200);

        self::assertNull($this->asJsonApi($response)->attributes()->get('name'));

        $name = $this->randString();

        $response = $this->withAuthHeader()->json(
            'POST',
            route('sessions.pois.create', $sessionJsonApi->id()),
            $this->asData(
                Mutator::TYPE,
                [
                    'type' => PoiReadonlyContract::BACKTRACK_TYPE,
                    'name' => $name,
                    'started_at' => dateTimeFormatted(new DateTime('2016-01-01 12:00:20')),
                    'ended_at' => dateTimeFormatted(new DateTime('2016-01-01 12:00:30')),
                ]
            )
        );

        $response->assertStatus(200);

        self::assertEquals($this->asJsonApi($response)->attributes()->get('name'), $name);

        $this->createProgress($sessionJsonApi, $connectingPayload, $this->asJsonApi($response));

        $response = $this->withAuthHeader()->json(
            'GET',
            route('sessions.pois.index', $sessionJsonApi->id())
        );

        $response->assertStatus(200);
        $jsonApi = $this->asJsonApi($response);

        self::assertCount(2, $jsonApi->asJsonApiCollection());
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

    /**
     * @return JsonApi
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function createService(): JsonApi
    {
        $response = $this->withAuthHeader()->json(
            'POST',
            '/services',
            $this->asData(
                ServiceMutator::TYPE,
                [
                    'name' => $this->randString(),
                ]
            )
        );

        $response->assertStatus(201);

        return $this->asJsonApi($response);
    }

    /**
     * @return array
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function createTeamAndSchool(): array
    {
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

        return [
            'team_id' => $team->id(),
            'school_id' => $school->id(),
        ];
    }
}
