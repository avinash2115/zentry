<?php

namespace App\Components\Sessions\Tests\Unit\Traits;

use App\Components\Sessions\Session\Poi\PoiContract;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Team\School\SchoolContract;
use App\Components\Users\Team\TeamContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Tags;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait SessionHelperTestTrait
 *
 * @package App\Components\Sessions\Tests\Unit\Traits
 */
trait SessionHelperTestTrait
{
    use HelperTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctPoiDataProvider(): array
    {
        return [
            [PoiReadonlyContract::POI_TYPE, new DateTime(), (new DateTime())->add(new DateInterval('PT10S')), null,],
            [
                PoiReadonlyContract::BACKTRACK_TYPE,
                new DateTime(),
                (new DateTime())->add(new DateInterval('PT10S')),
                $this->randString(),
            ],
            [
                PoiReadonlyContract::STOPWATCH_TYPE,
                new DateTime(),
                (new DateTime())->add(new DateInterval('PT10S')),
                $this->randString(),
            ],
        ];
    }

    /**
     * @param Identity        $identity
     * @param string          $type
     * @param DateTime        $startedAt
     * @param DateTime        $endedAt
     * @param SessionContract $session
     * @param array           $tags
     * @param string|null     $name
     * @param string|null     $reference
     *
     * @return PoiContract
     * @throws BindingResolutionException
     */
    private function createPoi(
        Identity $identity,
        string $type,
        DateTime $startedAt,
        DateTime $endedAt,
        SessionContract $session,
        array $tags = [],
        string $name = null
    ): PoiContract {
        return app()->make(
            PoiContract::class,
            [
                'identity' => $identity,
                'type' => $type,
                'name' => $name,
                'tags' => new Tags($tags),
                'user' => $this->createUser(),
                'session' => $session,
                'startedAt' => $startedAt,
                'endedAt' => $endedAt,
            ]
        );
    }

    /**
     * @param Identity             $id
     * @param string               $name
     * @param Tags                 $tags
     * @param UserReadonlyContract $user
     * @param string               $type
     * @param string               $description
     * @param DateTime|null        $scheduledOn
     * @param DateTime|null        $scheduledTo
     * @param string|null          $reference
     *
     * @return SessionContract
     * @throws BindingResolutionException
     */
    protected function createSession(
        Identity $id,
        string $name,
        Tags $tags,
        UserReadonlyContract $user,
        string $type = SessionReadonlyContract::TYPE_DEFAULT,
        string $description = '',
        string $reference = null,
        ?DateTime $scheduledOn = null,
        ?DateTime $scheduledTo = null
    ): SessionContract {
        return app()->make(
            SessionContract::class,
            [
                'identity' => $id,
                'name' => $name,
                'type' => $type,
                'description' => $description,
                'reference' => $reference,
                'tags' => $tags,
                'school' => $this->createSchool($this->createTeam($user)),
                'user' => $user,
                'scheduledOn' => $scheduledOn,
                'scheduledTo' => $scheduledTo,
            ]
        );
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return TeamContract
     * @throws BindingResolutionException
     */
    protected function createTeam(UserReadonlyContract $user): TeamContract
    {
        return app()->make(
            TeamContract::class,
            [
                'identity' => $this->generateIdentity(),
                'owner' => $user,
                'name' => $this->randString()
            ]
        );
    }

    /**
     * @param TeamContract $team
     *
     * @return SchoolContract
     * @throws BindingResolutionException
     */
    protected function createSchool(TeamContract $team): SchoolContract
    {
        return app()->make(
            SchoolContract::class,
            [
                'team' => $team,
                'identity' => $this->generateIdentity(),
                'name' => $this->randString(),
                'available' => true,
            ]
        );
    }

    /**
     * @param bool $persistedUser
     *
     * @return SessionContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    protected function createFullSessionWithPois(bool $persistedUser = false): SessionContract
    {
        if ($persistedUser) {
            $user = $this->createPersistedUser();
        } else {
            $user = $this->createUser();
        }

        $session = $this->createSession(IdentityGenerator::next(), $this->randString(5), new Tags([]), $user);

        collect($this->correctPoiDataProvider())->each(
            function (array $data) use ($session) {
                [$type, $startedAt, $endedAt, $name] = $data;
                $poi = $this->createPoi($this->generateIdentity(), $type, $startedAt, $endedAt, $session, [], $name);
                $session->addPoi($poi);
            }
        );

        return $session;
    }

    /**
     * @return ParticipantContract
     * @throws BindingResolutionException
     */
    private function participant(): ParticipantContract
    {
        return app()->make(
            ParticipantContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->createUser(),
                'email' => $this->email($this->randString(6)),
                'team' => null,
                'school' => null,
            ]
        );
    }
}
