<?php

namespace App\Components\Sessions\Services\Note;

use App\Assistants\Files\Services\Contracts\HasFiles;
use App\Components\Sessions\Session\Note\NoteDTO;
use App\Components\Sessions\Session\Note\NoteReadonlyContract;
use App\Components\Sessions\ValueObjects\Note\Payload;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface NoteServiceContract
 *
 * @package App\Components\Sessions\Services\Note
 */
interface NoteServiceContract extends HasFiles, IdentifiableContract
{
    /**
     * @param string $id
     *
     * @return NoteServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|NotFoundException|UnexpectedValueException
     */
    public function workWith(string $id): NoteServiceContract;

    /**
     * @return NoteReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): NoteReadonlyContract;

    /**
     * @return NoteDTO
     * @throws BindingResolutionException|NotFoundException|UnexpectedValueException|InvalidArgumentException|PropertyNotInit|RuntimeException
     */
    public function dto(): NoteDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function listRO(): Collection;

    /**
     * @param Payload           $payload
     * @param UploadedFile|null $file
     *
     * @return NoteServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException
     * @throws PropertyNotInit|RuntimeException|InvalidArgumentException
     */
    public function create(Payload $payload, UploadedFile $file = null): NoteServiceContract;

    /**
     * @param array $data
     *
     * @return NoteServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function change(array $data): NoteServiceContract;

    /**
     * @return NoteServiceContract
     * @throws BindingResolutionException|PropertyNotInit|NotFoundException|DeleteException
     */
    public function remove(): NoteServiceContract;
}
