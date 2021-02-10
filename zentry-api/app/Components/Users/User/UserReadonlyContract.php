<?php

namespace App\Components\Users\User;

use App\Assistants\Files\Services\Contracts\AsFileNamespace;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\Backtrack\BacktrackReadonlyContract;
use App\Components\Users\User\Poi\PoiReadonlyContract;
use App\Components\Users\User\Profile\ProfileReadonlyContract;
use App\Convention\Entities\Contracts\ArchivableReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface UserReadonlyContract
 *
 * @package App\Components\Users\User
 */
interface UserReadonlyContract extends IdentifiableContract, TimestampableContract, ArchivableReadonlyContract, AsFileNamespace
{
    /**
     * @return string
     */
    public function email(): string;

    /**
     * @return string
     */
    public function password(): string;

    /**
     * @return ProfileReadonlyContract
     * @throws InvalidArgumentException
     */
    public function profileReadonly(): ProfileReadonlyContract;

    /**
     * @return PoiReadonlyContract
     * @throws BindingResolutionException
     */
    public function poi(): PoiReadonlyContract;

    /**
     * @return BacktrackReadonlyContract
     * @throws RuntimeException|BindingResolutionException
     */
    public function backtrack(): BacktrackReadonlyContract;

    /**
     * @return Collection
     */
    public function storages(): Collection;

    /**
     * @return Collection
     */
    public function crms(): Collection;

    /**
     * @return StorageReadonlyContract
     * @throws NotFoundException
     */
    public function enabledStorage(): StorageReadonlyContract;

    /**
     * @return Collection
     */
    public function dataProviders(): Collection;
}
