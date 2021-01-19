<?php

namespace App\Components\Users\Tests\Unit\Integration;

use App\Components\Users\Participant\Mutators\DTO\Mutator as ParticipantMutator;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\CRM\Services\Source\Traits\SourceServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\Team\Mutators\DTO\Mutator as TeamMutator;
use App\Components\Users\Tests\Unit\Traits\CRMHelperTestTrait;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Users\User\CRM\CRMContract;
use Exception;
use Tests\IntegrationTestCase;

/**
 * Class SourceIntegrationTest
 *
 * @package App\Components\Users\Tests\Unit\Integration
 */
class SourceIntegrationTest extends IntegrationTestCase
{
    use CRMHelperTestTrait;
    use TeamServiceTrait;
    use ParticipantServiceTrait;
    use SourceServiceTrait;

    /**
     * @throws Exception
     */
    public function testTeamSource(): void
    {
        $this->setToken();
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
        $teamResponse = $this->asJsonApi($response);
        self::assertFalse($teamResponse->attributes()->get('imported'));

        $team = $this->teamService__()->workWith($teamResponse->id())->readonly();

        $this->createSource($team);

        $response = $this->withAuthHeader()->json(
            'GET',
            '/teams'
        );

        $response->assertStatus(200);
        //@todo don't remove
        //        self::assertTrue($this->asJsonApi($response)->asJsonApiCollection()->first()->attributes()->get('imported'));
    }

    /**
     * @throws Exception
     */
    public function testParticipantSource(): void
    {
        $this->setToken();
        $response = $this->withAuthHeader()->json(
            'POST',
            '/participants',
            $this->asData(
                ParticipantMutator::TYPE,
                [
                    'email' => $this->randString() . '@example.com',
                    'firstName' => $this->randString(),
                    'lastName' => $this->randString(),
                ]
            )
        );
        $participantResponse = $this->asJsonApi($response);
        self::assertFalse($participantResponse->attributes()->get('imported'));

        $participant = $this->participantService__()->workWith($participantResponse->id())->readonly();

        $this->createSource($participant);

        $response = $this->withAuthHeader()->json(
            'GET',
            '/participants'
        );

        $response->assertStatus(200);
        //@todo don't remove
        //        self::assertTrue($this->asJsonApi($response)->asJsonApiCollection()->first()->attributes()->get('imported'));
    }

    /**
     * @param CRMImportableContract $owner
     *
     * @return void
     * @throws Exception
     */
    protected function createSource(CRMImportableContract $owner): void
    {
        $user = $this->getUser();
        $crmService = $this->userService__()->workWith($user->identity())->crmService();

        $crmService->connect(
            [
                'driver' => CRMContract::DRIVER_THERAPYLOG,
                'config' => [
                    'email' => $this->randString() . '@mail.com',
                    'password' => $this->randString(),
                ],
            ]
        );
        $this->sourceService__()->create(
                $crmService->readonly(),
                $owner,
                [
                    'source_id' => $this->randString(),
                ]
            );
        $this->flush();
    }
}
