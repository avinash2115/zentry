<?php

namespace App\Components\Providers\ProviderService;

use App\Components\CRM\Source\ProviderSourceEntity;
use App\Components\CRM\Source\Traits\HasSourceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class ProviderEntity
 *
 * @package App\Components\Providers\ProviderService
 */
class ProviderEntity implements ProviderContract
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use CollectibleTrait;
    use HasSourceTrait;

    /**
     * @var string
     */
    private string $name;

     /**
     * @var string
     */
    private string $code;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     * @param string               $name
     * @param string|null          $code
     *
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        UserReadonlyContract $user,
        string $name,
        string $code = null
      
    ) {
        $this->setIdentity($identity);

        $this->setUser($user)->changeName($name);
        $this->changeCode($code);

        $this->setSources();
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritdoc
     */
    public function user(): UserReadonlyContract
    {
        return $this->user;
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return ProviderContract
     */
    private function setUser(UserReadonlyContract $user): ProviderContract
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return $this->code;
    }
    /**
     * @inheritDoc
     */
    public function changeName(string $value): ProviderContract
    {
        if (strEmpty($value)) {
            throw new InvalidArgumentException("Name can't be empty");
        }

        $this->name = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeCode(string $value): ProviderContract
    {
        if (strEmpty($value)) {
            throw new InvalidArgumentException("Code can't be empty");
        }

        $this->code = $value;

        return $this;
    }

    

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_PROVIDER;
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return ProviderSourceEntity::class;
    }
}
