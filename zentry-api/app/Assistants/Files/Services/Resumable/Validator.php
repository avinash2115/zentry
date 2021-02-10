<?php

namespace App\Assistants\Files\Services\Resumable;

use App\Assistants\Files\Exceptions\Resumable\IsNotInstantiableException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile as IlluminateUploadedFile;
use Illuminate\Support\Arr;
use Kladislav\LaravelChunkUpload\Config\AbstractConfig;
use Kladislav\LaravelChunkUpload\Handler\AbstractHandler;
use Kladislav\LaravelChunkUpload\Storage\ChunkStorage;
use League\Flysystem\FileNotFoundException as LeagueFileNotFoundException;

/**
 * Interface FileServiceContract
 *
 * @package App\Assistants\Files\Services
 */
class Validator
{
    public const CHUNK_SIZE = 'resumableChunkSize';

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var AbstractHandler|null
     */
    private ?AbstractHandler $handler = null;

    /**
     * @var ChunkStorage
     */
    private ChunkStorage $chunkStorage;

    /**
     * @param IlluminateUploadedFile|string|null $filenameOrFile
     * @param Request                       $request
     * @param string                        $handlerClass
     * @param ChunkStorage                  $chunkStorage
     */
    public function __construct($filenameOrFile, Request $request, string $handlerClass, ChunkStorage $chunkStorage)
    {
        $this->chunkStorage = $chunkStorage;
        $this->request = $request;

        if ($filenameOrFile !== null && $handlerClass) {
            $this->handler = new $handlerClass($request, $this->_file($filenameOrFile), AbstractConfig::config());
        }
    }

    /**
     * @param IlluminateUploadedFile|string $filenameOrFile
     *
     * @return IlluminateUploadedFile
     */
    private function _file($filenameOrFile): IlluminateUploadedFile
    {
        return $filenameOrFile instanceof IlluminateUploadedFile ? $filenameOrFile : $this->fakeFile($filenameOrFile);
    }

    /**
     * @param string $filename
     *
     * @return IlluminateUploadedFile
     */
    private function fakeFile(string $filename): IlluminateUploadedFile
    {
        return IlluminateUploadedFile::fake()->create($filename);
    }

    /**
     * @return bool
     * @throws IsNotInstantiableException
     */
    public function isPartUploaded(): bool
    {
        try {
            $metadata = $this->chunkStorage->disk()->getMetadata(
                $this->chunkStorage->directory() . $this->_handler()->getChunkFileName()
            );
        } catch (LeagueFileNotFoundException|FileNotFoundException $exception) {
            return false;
        }

        if (is_array($metadata)) {
            if (Arr::get($metadata, 'size') !== $this->request->get(self::CHUNK_SIZE)) {
                $this->chunkStorage->disk()->delete($this->chunkStorage->directory() . $this->_handler()->getChunkFileName());

                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @return AbstractHandler
     * @throws IsNotInstantiableException
     */
    private function _handler(): AbstractHandler
    {
        if (!$this->handler instanceof AbstractHandler) {
            throw new IsNotInstantiableException();
        }

        return $this->handler;
    }
}
