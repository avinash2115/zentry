<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\Tests\Unit\Traits\CRMHelperTestTrait;
use App\Components\Users\Tests\Unit\Traits\TeamHelperTestTrait;
use App\Components\Users\User\CRM\CRMContract;
use App\Components\CRM\Source\SourceContract;
use App\Components\CRM\Source\TeamSourceEntity;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;
use \Exception;
use \TypeError;

/**
 * Class SourceEntityTest
 */
class SourceEntityTest extends TestCase
{
    use CRMHelperTestTrait;
    use TeamHelperTestTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            [$this->randString(), $this->randString()],
            ['', ''],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [TeamSourceEntity::class, $this->randString()],
        ];
    }

    /**
     * @param string $sourceId
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $sourceId
    ): void {
        $this->expectException(TypeError::class);

        $user = $this->createUser();
        $crm = $this->createCRM($user);
        $this->make($this->generateIdentity(), $crm, $user, $sourceId);
    }

    /**
     * @param string $type
     * @param string $sourceId
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $type,
        string $sourceId
    ): void {
        $user = $this->createUser();
        $crm = $this->createCRM($user);
        $owner = $this->createTeam($user);
        $identity = $this->generateIdentity();

        $entity = $this->make($identity, $crm, $owner, $sourceId);

        static::assertTrue($entity->identity()->equals($identity));

        static::assertEquals($entity->owner()->sourceEntityClass(), $type);
        static::assertEquals($entity->owner()->identity()->toString(), $owner->identity()->toString());
        static::assertEquals($entity->sourceId(), $sourceId);

        static::assertNotNull($entity->createdAt());
    }

    /**
     * @param Identity              $identity
     * @param CRMContract           $crm
     * @param CRMImportableContract $owner
     * @param string                $sourceId
     *
     * @return SourceContract
     * @throws BindingResolutionException
     */
    protected function make(
        Identity $identity,
        CRMContract $crm,
        CRMImportableContract $owner,
        string $sourceId
    ): SourceContract {
        return app()->make(
            $owner->sourceEntityClass(),
            [
                'identity' => $identity,
                'crm' => $crm,
                'owner' => $owner,
                'sourceId' => $sourceId,
            ]
        );
    }
}
