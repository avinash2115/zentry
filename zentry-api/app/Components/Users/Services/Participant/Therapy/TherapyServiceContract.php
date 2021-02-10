<?php

namespace App\Components\Users\Services\Participant\Therapy;

use App\Components\Users\Participant\Therapy\TherapyDTO;
use App\Components\Users\Participant\Therapy\TherapyReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface TherapyServiceContract
 *
 * @package App\Components\Users\Services\Participant\Therapy
 */
interface TherapyServiceContract
{
    /**
     * @return TherapyReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): TherapyReadonlyContract;

    /**
     * @return TherapyDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): TherapyDTO;

    /**
     * @param array $data
     *
     * @return TherapyServiceContract
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function change(array $data): TherapyServiceContract;
}
