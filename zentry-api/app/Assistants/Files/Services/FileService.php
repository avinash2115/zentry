<?php

namespace App\Assistants\Files\Services;

use App\Assistants\Files\Exceptions\Temporary\Url\NotFoundOrExpired;
use App\Assistants\Files\Services\Contracts\HasFiles;
use App\Assistants\Files\ValueObjects\DownloadResponseVO;
use App\Assistants\Files\ValueObjects\File as FileVO;
use App\Assistants\Files\ValueObjects\Metadata;
use App\Assistants\Files\ValueObjects\TemporaryUrl;
use App\Convention\Exceptions\Storage\Dir\CreateException;
use App\Convention\Exceptions\Storage\Dir\RemoveException;
use App\Convention\Exceptions\Storage\File\CopyException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Storage\File\MoveException;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Storage\File\UploadException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Helpers\FileUtility;
use Cache;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use League\Flysystem\Filesystem;
use League\Flysystem\AdapterInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class FileService
 *
 * @package App\Assistants\Files\Services
 */
class FileService implements FileServiceContract
{
    /**
     * @var FilesystemAdapter
     */
    public FilesystemAdapter $storage;

    /**
     * FileService constructor.
     *
     * @param FilesystemAdapter $storage
     */
    public function __construct(FilesystemAdapter $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     */
    public function put(
        UploadedFile $uploadedFile,
        HasFiles $supplier,
        bool $isPublic = false
    ): FileVO {
        $displayName = $uploadedFile->getClientOriginalName();

        if ($displayName === null) {
            throw new InvalidArgumentException('Empty file name.');
        }

        if ($uploadedFile->getRealPath() === false) {
            throw new RuntimeException('Cannot obtain file');
        }

        $destinationName = IdentityGenerator::next() . ".{$uploadedFile->getClientOriginalExtension()}";

        $stream = fopen($uploadedFile->getRealPath(), 'rb');

        if ($stream === false) {
            throw new UploadException("File {$displayName} not found and can't be uploaded.");
        }

        return $this->putContents($supplier->fileNamespace(), $destinationName, $displayName, $stream);
    }

    /**
     * @inheritdoc
     */
    public function putContents(
        string $destinationPath,
        string $destinationName,
        string $displayName,
        $contents,
        $options = []
    ): FileVO {
        $destinationPath = FileUtility::sanitizePath($destinationPath);
        $destinationName = FileUtility::sanitizeName($destinationName);
        $displayName = FileUtility::sanitizeName($displayName);

        $fullDestinationPath = $destinationPath[strlen(
            $destinationPath
        ) - 1] === DIRECTORY_SEPARATOR ? $destinationPath . $destinationName : $destinationPath . DIRECTORY_SEPARATOR . $destinationName;

        $result = $this->storage->put($fullDestinationPath, $contents);

        if (is_resource($contents)) {
            fclose($contents);
        }

        if ($result === false || !$this->storage->exists($fullDestinationPath)) {
            throw new UploadException("File {$displayName} can't be uploaded.");
        }

        return app()->make(
            FileVO::class,
            [
                'url' => $fullDestinationPath,
                'name' => $displayName,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function putFileAs(string $destinationPath, string $destinationName, $file, array $options = []): FileVO
    {
        $destinationPath = FileUtility::sanitizePath($destinationPath);
        $destinationName = FileUtility::sanitizeName($destinationName);

        $fullDestinationPath = FileUtility::sanitizePath($destinationPath . DIRECTORY_SEPARATOR . $destinationName);

        $result = $this->storage->putFileAs($destinationPath, $file, $destinationName, $options);

        if ($result === false || !$this->storage->exists($fullDestinationPath)) {
            throw new UploadException("File {$destinationName} can't be uploaded.");
        }

        return app()->make(
            FileVO::class,
            [
                'url' => $fullDestinationPath,
                'name' => $destinationName,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function copy(
        string $originalFullPath,
        string $originalDisplayName,
        HasFiles $supplier,
        bool $isPublic = false
    ): FileVO {
        $fullDestinationPath = $supplier->filePath($originalDisplayName);

        $result = $this->storage->copy($originalFullPath, $fullDestinationPath);

        if ($result === false || !$this->storage->exists($fullDestinationPath)) {
            throw new CopyException("Error while copy {$originalDisplayName}.");
        }

        return app()->make(
            FileVO::class,
            [
                'url' => $isPublic ? $this->storage->url($fullDestinationPath) : $fullDestinationPath,
                'name' => $originalDisplayName,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function moveRaw(string $originalFullPath, string $destinationFullPath): bool
    {
        $result = $this->storage->move($originalFullPath, $destinationFullPath);;

        if ($result === false || !$this->storage->exists($destinationFullPath)) {
            throw new MoveException("Error while moving {$destinationFullPath}.");
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function download(string $pathToFileWithName, string $fileName): DownloadResponseVO
    {
        return new DownloadResponseVO(
            $this->get(
                $pathToFileWithName
            ), [
                'Content-Description' => 'File downloading',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Content-Type' => $this->storage->mimeType($pathToFileWithName),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function downloadViaTemporaryURL(string $id): DownloadResponseVO
    {
        $url = Cache::get(self::CACHE_TEMPORARY_PREFIX . $id);

        if (is_string($url)) {
            Cache::forget(self::CACHE_TEMPORARY_PREFIX . $id);

            [$pathToFileWithName, $fileName] = explode(self::CACHE_TEMPORARY_DELIMETER, $url);

            if (!$this->storage->exists($pathToFileWithName)) {
                throw new FileNotFoundException('File not found');
            }

            return $this->download($pathToFileWithName, $fileName);
        }

        throw new NotFoundOrExpired();
    }

    /**
     * @inheritDoc
     */
    public function temporaryUrl(string $pathToFileWithName, string $fileName, int $ttl = 0): TemporaryUrl
    {
        if (!$ttl) {
            $ttl = (int)config('files.url.temporary.ttl');
        }

        try {
            return new TemporaryUrl(
                $fileName, $this->storage->temporaryUrl($pathToFileWithName, now()->addSeconds($ttl))
            );
        } catch (RuntimeException $exception) {
            $identity = IdentityGenerator::next();

            $url = new TemporaryUrl(
                $fileName, route('files.temporary_url.download', ['id' => (string)$identity]), $identity
            );

            Cache::set(
                self::CACHE_TEMPORARY_PREFIX . $identity,
                $pathToFileWithName . self::CACHE_TEMPORARY_DELIMETER . $fileName,
                $ttl
            );

            return $url;
        }
    }

    /**
     * @inheritDoc
     */
    public function isEmptyDir(string $path): bool
    {
        $path = rtrim($path, '/');

        return 0 === count($this->storage->directories($path)) && 0 === count($this->storage->files($path));
    }

    /**
     * @inheritDoc
     */
    public function isExist(string $pathToFileWithName): bool
    {
        return $this->storage->exists($pathToFileWithName);
    }

    /**
     * @inheritdoc
     */
    public function remove(string $fullFileName): bool
    {
        if (!$this->storage->delete($fullFileName) || $this->storage->exists($fullFileName)) {
            throw new DeleteException("Error while removing file: {$fullFileName}");
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(string $path): string
    {
        return $this->storage->get($path);
    }

    /**
     * @inheritDoc
     */
    public function asStreamedResponse(string $pathToFileWithName, string $fileName): StreamedResponse
    {
        return $this->storage->response($pathToFileWithName, $fileName);
    }

    /**
     * @inheritDoc
     */
    public function asResource(string $pathToFileWithName)
    {
        $stream = $this->storage->readStream($pathToFileWithName);

        if (!is_resource($stream)) {
            throw new ReadException("Can't read file");
        }

        return $stream;
    }

    /**
     * @inheritDoc
     */
    public function asBinaryContent(
        string $pathToFileWithName,
        bool $deleteFileAfterSend = false
    ): BinaryFileResponse {
        return (new BinaryFileResponse(
            $this->path($pathToFileWithName), 200, [], true, 'attachment'
        ))->deleteFileAfterSend(
            $deleteFileAfterSend
        );
    }

    /**
     * @inheritDoc
     */
    public function metadata(string $pathToFileWithName): Metadata
    {
        if (!$this->isExist($pathToFileWithName)) {
            throw new ReadException();
        }

        $metadata = $this->storage->getMetadata($pathToFileWithName);

        if (!$metadata) {
            throw new ReadException();
        }

        return new Metadata($metadata);
    }

    /**
     * @inheritDoc
     */
    public function mimeType(string $pathToFileWithName): string
    {
        if (!$this->isExist($pathToFileWithName)) {
            throw new ReadException();
        }

        $mimeType = $this->storage->getMimetype($pathToFileWithName);

        if (!is_string($mimeType)) {
            throw new ReadException();
        }

        return $mimeType;
    }

    /**
     * @inheritDoc
     */
    public function url(string $path): string
    {
        return $this->storage->url($path);
    }

    /**
     * @inheritDoc
     */
    public function createDir(string $dir): bool
    {
        if (!$this->storage->createDir($dir)) {
            throw new CreateException("Error while creation dir {$dir}");
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteDir(string $dir): bool
    {
        if ($this->storage->exists($dir) && !$this->storage->deleteDir($dir)) {
            throw new RemoveException("Error while deletion dir {$dir}");
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function path(string $path, bool $init = false): string
    {
        if ($init) {
            $this->storage->put($path, '');
        }

        return $this->storage->path($path);
    }

    /**
     * @inheritDoc
     */
    public function list(string $directory = null, bool $recursive = false): Collection
    {
        return collect($this->storage->files($directory, $recursive));
    }

    /**
     * @inheritDoc
     */
    public function merge(Collection $paths, string $displayName, HasFiles $supplier): FileVO
    {
        $extension = pathinfo($displayName, PATHINFO_EXTENSION);
        $destinationName = IdentityGenerator::next()->toString() . ".{$extension}";
        $destinationFile = $this->putContents($supplier->fileNamespace(), $destinationName, $displayName, '');

        $stream = fopen($this->path($destinationFile->url()), 'a+b');

        if (!is_resource($stream)) {
            throw new ReadException();
        }

        $paths->sortBy(function (string $relativePath) {
            return $this->metadata($relativePath)->timestamp();
        })->each(
            function (string $relativePath) use ($stream) {
                $resource = $this->asResource($relativePath);

                stream_copy_to_stream($resource, $stream);

                fclose($resource);

                $this->remove($relativePath);
            }
        );

        return $destinationFile;
    }

    /**
     * @inheritDoc
     */
    public function adapter(): AdapterInterface
    {
        if (!$this->storage->getDriver() instanceof Filesystem) {
            throw new RuntimeException('Storage is not instance of Filesystem and not contains getAdapter method.');
        }

        return $this->storage->getDriver()->getAdapter();
    }
}
