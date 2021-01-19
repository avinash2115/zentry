<?php

namespace App\Assistants\Files\Services;

use App\Assistants\Files\Exceptions\Temporary\Url\NotFoundOrExpired;
use App\Assistants\Files\Services\Contracts\HasFiles;
use App\Assistants\Files\ValueObjects\DownloadResponseVO;
use App\Assistants\Files\ValueObjects\File;
use App\Assistants\Files\ValueObjects\Metadata;
use App\Assistants\Files\ValueObjects\TemporaryUrl;
use App\Convention\Exceptions\Storage\Dir\CreateException;
use App\Convention\Exceptions\Storage\Dir\RemoveException;
use App\Convention\Exceptions\Storage\File\CopyException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Storage\File\MoveException;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Storage\File\UploadException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\File as IlluminateFile;
use Illuminate\Http\UploadedFile as IlluminateUploadedFile;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException as LeagueFileNotFoundException;
use League\Flysystem\RootViolationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Interface FileServiceContract
 *
 * @package App\Assistants\Files\Services
 */
interface FileServiceContract
{
    public const CACHE_TEMPORARY_PREFIX = 'file_temporary_access_internal';

    public const CACHE_TEMPORARY_DELIMETER = ':::';

    /**
     * @param UploadedFile $uploadedFile
     * @param HasFiles     $supplier
     * @param bool         $isPublic
     *
     * @return File
     * @throws UploadException|InvalidArgumentException|RuntimeException
     */
    public function put(
        UploadedFile $uploadedFile,
        HasFiles $supplier,
        bool $isPublic = false
    ): File;

    /**
     * @param string $destinationPath
     * @param string $destinationName
     * @param string $displayName
     * @param mixed  $contents
     * @param array  $options
     *
     * @return File
     * @throws UploadException|BindingResolutionException
     */
    public function putContents(
        string $destinationPath,
        string $destinationName,
        string $displayName,
        $contents,
        $options = []
    ): File;

    /**
     * @param string                                $destinationPath
     * @param string                                $destinationName
     * @param IlluminateFile|IlluminateUploadedFile $file
     * @param array                                 $options
     *
     * @return File
     * @throws UploadException|BindingResolutionException
     */
    public function putFileAs(string $destinationPath, string $destinationName, $file, array $options = []): File;

    /**
     * @param string   $originalFullPath
     * @param string   $originalDisplayName
     * @param HasFiles $supplier
     * @param bool     $isPublic
     *
     * @return File
     * @throws CopyException|BindingResolutionException|RuntimeException
     */
    public function copy(
        string $originalFullPath,
        string $originalDisplayName,
        HasFiles $supplier,
        bool $isPublic = false
    ): File;

    /**
     * @param string $originalFullPath
     * @param string $destinationFullPath
     *
     * @return bool
     * @throws MoveException|BindingResolutionException|RuntimeException
     */
    public function moveRaw(
        string $originalFullPath,
        string $destinationFullPath
    ): bool;

    /**
     * @param string $pathToFileWithName
     * @param string $fileName
     *
     * @return DownloadResponseVO
     * @throws FileNotFoundException
     */
    public function download(string $pathToFileWithName, string $fileName): DownloadResponseVO;

    /**
     * @param string $pathToFileWithName
     * @param string $fileName
     *
     * @return StreamedResponse
     * @throws FileNotFoundException|ReadException
     */
    public function asStreamedResponse(string $pathToFileWithName, string $fileName): StreamedResponse;

    /**
     * @param string $pathToFileWithName
     *
     * @return resource
     * @throws FileNotFoundException|ReadException
     */
    public function asResource(string $pathToFileWithName);

    /**
     * Only for local storage driver
     *
     * @param string $pathToFileWithName
     * @param bool   $deleteFileAfterSend
     *
     * @return BinaryFileResponse
     * @throws FileNotFoundException
     */
    public function asBinaryContent(
        string $pathToFileWithName,
        bool $deleteFileAfterSend = false
    ): BinaryFileResponse;

    /**
     * @param string $pathToFileWithName
     *
     * @return Metadata
     * @throws LeagueFileNotFoundException
     * @throws ReadException
     */
    public function metadata(string $pathToFileWithName): Metadata;

    /**
     * @param string $pathToFileWithName
     *
     * @return string
     * @throws LeagueFileNotFoundException
     * @throws ReadException
     */
    public function mimeType(string $pathToFileWithName): string;

    /**
     * @param string $id
     *
     * @return DownloadResponseVO
     * @throws FileNotFoundException|NotFoundOrExpired
     */
    public function downloadViaTemporaryURL(string $id): DownloadResponseVO;

    /**
     * @param string $pathToFileWithName
     * @param string $fileName
     * @param int    $ttl
     *
     * @return TemporaryUrl
     * @throws RuntimeException
     */
    public function temporaryUrl(string $pathToFileWithName, string $fileName, int $ttl = 0): TemporaryUrl;

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isEmptyDir(string $path): bool;

    /**
     * @param string $pathToFileWithName
     *
     * @return bool
     */
    public function isExist(string $pathToFileWithName): bool;

    /**
     * @param string $fullFileName
     *
     * @return bool
     * @throws DeleteException
     */
    public function remove(string $fullFileName): bool;

    /**
     * @param string $path
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function get(string $path): string;

    /**
     * @param string $path
     *
     * @return string
     * @throws RuntimeException
     */
    public function url(string $path): string;

    /**
     * @param string $dir
     *
     * @return bool
     * @throws CreateException
     */
    public function createDir(string $dir): bool;

    /**
     * @param string $dir
     *
     * @return bool
     * @throws RemoveException|RootViolationException
     */
    public function deleteDir(string $dir): bool;

    /**
     * @param string $path
     * @param bool   $init
     *
     * @return string
     */
    public function path(string $path, bool $init = false): string;

    /**
     * @param string $directory
     * @param bool   $recursive
     *
     * @return Collection
     */
    public function list(string $directory = null, bool $recursive = false): Collection;

    /**
     * @param Collection $paths
     * @param string     $displayName
     * @param HasFiles   $supplier
     *
     * @return File
     * @throws InvalidArgumentException
     * @throws UploadException
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function merge(Collection $paths, string $displayName, HasFiles $supplier): File;

    /**
     * @return AdapterInterface
     * @throws RuntimeException
     */
    public function adapter(): AdapterInterface;
}
