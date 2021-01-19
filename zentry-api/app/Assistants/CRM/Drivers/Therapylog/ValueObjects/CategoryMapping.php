<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects;

use InvalidArgumentException;

/**
 * Class CategoryMapping
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects
 */
class CategoryMapping
{
    /**
     * @var string
     */
    private $category;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var array
     */
    private $models;

    /**
     * CategoryMapping constructor.
     *
     * @param string $category
     * @param array  $roles
     * @param array  $models
     */
    public function __construct(string $category, array $roles, array $models)
    {
        if (strEmpty($category)) {
            throw new InvalidArgumentException("Category can not be empty");
        }
        $this->category = $category;
        $this->roles = $roles;
        $this->models = $models;
    }

    /**
     * @return string
     */
    public function category(): string
    {
        return $this->category;
    }

    /**
     * @return array
     */
    public function roles(): array
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function models(): array
    {
        return $this->models;
    }

}
