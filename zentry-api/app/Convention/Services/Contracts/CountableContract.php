<?php

namespace App\Convention\Services\Contracts;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface CountableContract
 *
 * @package App\Convention\Services\Contracts
 */
interface CountableContract
{
    /**
     * @return int
     * @throws BindingResolutionException|NonUniqueResultException|NoResultException
     */
    public function count(): int;
}