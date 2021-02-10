<?php

namespace App\Components\Sessions\Services\Stream;

use App\Assistants\Files\Services\Contracts\HasFiles;
use App\Assistants\Files\Services\Contracts\HasTemporaryUrl;
use App\Components\Sessions\Session\Stream\StreamDTO;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Storage\File\UploadException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface StreamServiceContract
 *
 * @package App\Components\Sessions\Services\Stream
 */
interface StreamServiceContract extends HasFiles, HasTemporaryUrl
{
    /**
     * @param string $id
     *
     * @return StreamServiceContract
     * @throws NotFoundException|BindingResolutionException|UnexpectedValueException|InvalidArgumentException
     */
    public function workWith(string $id): StreamServiceContract;

    /**
     * @param string $type
     *
     * @return StreamServiceContract
     * @throws NotFoundException|BindingResolutionException|UnexpectedValueException
     */
    public function workWithType(string $type): StreamServiceContract;

    /**
     * @return Collection
     * @throws PropertyNotInit
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws PropertyNotInit
     */
    public function listRO(): Collection;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return StreamReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): StreamReadonlyContract;

    /**
     * @return StreamDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): StreamDTO;

    /**
     * @param UploadedFile $uploadedFile
     * @param string       $type
     *
     * @return StreamServiceContract
     * @throws NotFoundException|BindingResolutionException|PropertyNotInit|RuntimeException|UploadException|InvalidArgumentException
     */
    public function create(UploadedFile $uploadedFile, string $type): StreamServiceContract;

    /**
     * @param array $data
     *
     * @return StreamServiceContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function change(array $data): StreamServiceContract;

    /**
     * @return StreamServiceContract
     * @throws PropertyNotInit|NotFoundException|BindingResolutionException
     * @throws DeleteException
     */
    public function remove(): StreamServiceContract;

    /**
     * @param string       $type
     * @param UploadedFile $uploadedFile
     *
     * @return bool
     * @throws UploadException
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function receivePartial(string $type, UploadedFile $uploadedFile): bool;

    /**
     * @param string       $type
     *
     * @return StreamServiceContract
     * @throws PropertyNotInit|InvalidArgumentException|UploadException|BindingResolutionException|RuntimeException
     */
    public function mergePartial(string $type): StreamServiceContract;
}
