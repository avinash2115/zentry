<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\Participant\Goal\GoalContract;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\ValueObjects\Participant\Goal\Meta;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Class GoalEntityTest
 *
 * @package App\Components\Users\Tests\Unit\Entity
 */
class GoalEntityTest extends TestCase
{
    use HelperTrait;

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
     * @param string $name
     * @param array  $meta
     * @param string $description
     *
     * @throws BindingResolutionException
     * @throws Exception
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $name,
        array $meta,
        string $description
    ): void
    {
        $identity = $this->generateIdentity();
        $participant = $this->participant();

        $entity = $this->create($identity, $participant, $name, new Meta($meta), $description);

        static::assertTrue($entity->identity()->equals($identity));

        static::assertEquals($name, $entity->name());
        static::assertEquals($description, $entity->description());
        static::assertEquals((new Meta($meta))->toArray(), $entity->meta()->toArray());
        static::assertFalse($entity->isReached());

        $entity->reach();

        static::assertTrue($entity->isReached());

        $description = $this->randString();
        $entity->changeDescription($description);
        static::assertEquals($description, $entity->description());

        $participant->addGoal($entity);
        static::assertCount(1, $participant->goals());

    }

    /**
     * @param string $name
     * @param array  $meta
     * @param string $description
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $name,
        array $meta,
        string $description
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $participant = $this->participant();
        $this->create($this->generateIdentity(), $participant, $name, new Meta($meta), $description);
    }

    /**
     * @param Identity            $identity
     * @param ParticipantContract $participant
     * @param string              $name
     * @param Meta                $meta
     * @param string              $description
     *
     * @return GoalContract
     * @throws BindingResolutionException
     */
    private function create(
        Identity $identity,
        ParticipantContract $participant,
        string $name,
        Meta $meta,
        string $description
    ): GoalContract
    {
        return $this->app->make(
            GoalContract::class,
            [
                'identity' => $identity,
                'participant' => $participant,
                'name' => $name,
                'meta' => $meta,
                'description' => $description,
            ]
        );
    }

    /**
     * @return ParticipantContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function participant(): ParticipantContract
    {
        return $this->app->make(
            ParticipantContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->createUser(),
                'team' => null,
                'email' =>  $this->randEmail(),
                'firstName' => $this->randString(),
                'lastName' => $this->randString(),
                'phoneCode' => null,
                'phoneNumber' => null,
                'avatar' => null,
                'gender' => null,
                'dob' => null,
                'school' => null
            ]
        );
    }
}
