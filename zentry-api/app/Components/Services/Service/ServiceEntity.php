<?php

namespace App\Components\Services\Service;

use App\Components\CRM\Source\ServiceSourceEntity;
use App\Components\CRM\Source\Traits\HasSourceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class ServiceEntity
 *
 * @package App\Components\Services\Service
 */
class ServiceEntity implements ServiceContract
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
     * @var string
     */
    private string $category;

     /**
     * @var string
     */
    private string $status;

     /**
     * @var string
     */
    private string $actions;
    



    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     * @param string               $name
     * @param string|null          $code
     * @param string|null          $category
     * @param string|null          $status
     * @param string|null          $actions

     *
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        UserReadonlyContract $user,
        string $name,
        string $code = null,
        string $category = null,
        string $status = null,
        string $actions = null
    ) {
        $this->setIdentity($identity);

        $this->setUser($user)->changeName($name);
        $this->changeCode($code);
        $this->changeCategory($category);
        $this->changeStatus($status);
        $this->changeActions($actions);

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
     * @return ServiceContract
     */
    private function setUser(UserReadonlyContract $user): ServiceContract
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
    public function category(): string
    {
        return $this->category;
    }

     /**
     * @inheritDoc
     */
    public function status(): string
    {
        return $this->status;
    }
    
     /**
     * @inheritDoc
     */
    public function actions(): string
    {
        return $this->actions;
    }
    
    


    /**
     * @inheritDoc
     */
    public function changeName(string $value): ServiceContract
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
    public function changeCode(string $value): ServiceContract
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
    public function changeCategory(string $value): ServiceContract
    {
        if (strEmpty($value)) {
            throw new InvalidArgumentException("Category can't be empty");
        }

        $this->category = $value;

        return $this;
    }
     /**
     * @inheritDoc
     */
    public function changeStatus(string $value): ServiceContract
    {
        if (strEmpty($value)) {
            throw new InvalidArgumentException("status can't be empty");
        }

        $this->status = $value;

        return $this;
    }
     /**
     * @inheritDoc
     */
    public function changeActions(string $value): ServiceContract
    {
        if (strEmpty($value)) {
            throw new InvalidArgumentException("actions can't be empty");
        }

        $this->actions = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_SERVICE;
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return ServiceSourceEntity::class;
    }
}
