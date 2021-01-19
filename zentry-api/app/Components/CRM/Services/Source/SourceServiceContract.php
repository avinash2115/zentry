<?php

namespace App\Components\CRM\Services\Source;

use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\CRM\Source\SourceDTO;
use App\Components\CRM\Source\SourceReadonlyContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface SourceServiceContract
 *
 * @package App\Components\CRM\Services\Source
 */
interface SourceServiceContract extends FilterableContract
{
    /**
     * @param string $id
     *
     * @return SourceServiceContract
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function workWith(string $id): SourceServiceContract;

    /**
     * @return SourceReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): SourceReadonlyContract;

    /**
     * @return SourceDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): SourceDTO;

    /**
     * @param CRMReadonlyContract   $crm
     * @param CRMImportableContract $owner
     * @param array                 $data
     *
     * @return SourceServiceContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     * @throws RuntimeException
     * @throws NotFoundException
     */
    public function create(CRMReadonlyContract $crm, CRMImportableContract $owner, array $data): SourceServiceContract;

    /**
     * @return Collection
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function listRO(): Collection;

    /**
     * @return SourceServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function remove(): SourceServiceContract;

    /**
     * @param array $data
     *
     * @return SourceServiceContract
     */
    public function change(array $data): SourceServiceContract;

    /**
     * @param string $type
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function sourceEntityClass(string $type): string;
}
