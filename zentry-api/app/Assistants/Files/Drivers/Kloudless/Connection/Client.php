<?php

namespace App\Assistants\Files\Drivers\Kloudless\Connection;

use App\Assistants\Files\Drivers\Kloudless\Connection\Exception\RequestException;
use App\Assistants\Files\Drivers\Kloudless\Connection\Http\Request;
use App\Assistants\Files\Drivers\Kloudless\Connection\Http\Response;
use App\Assistants\Files\Drivers\Kloudless\ValueObjects\FileInfo;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Arr;
use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Client
 *
 * @package App\Assistants\Files\Drivers\Kloudless
 */
class Client
{
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->request = new Request($apiClient);
    }

    /**
     * @return Response
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function quota(): Response
    {
        return $this->_request()->get('/storage/quota');
    }

    /**
     * @param string $path
     * @param array  $params
     *
     * @return Response
     * @throws NotImplementedException
     */
    public function getMetadata(string $path, array $params = []): Response
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @param array $parts
     *
     * @return string
     * @throws InvalidArgumentException
     * @throws RequestException
     * @throws RuntimeException
     */
    public function createFolder(array $parts): string
    {
        $folderId = 'root';

        foreach ($parts as $part) {
            $folderId = $this->createFolderRecursive($part, $folderId);
        }

        return $folderId;
    }

    /**
     * @param string $path
     * @param string $folderId
     *
     * @return string
     * @throws InvalidArgumentException
     * @throws RequestException
     * @throws RuntimeException
     * @throws Exception
     */
    private function createFolderRecursive(string $path, string $folderId): string
    {
        $path = trim($path, DIRECTORY_SEPARATOR);

        $response = $this->_request()->postJson(
            '/storage/folders/',
            [
                'name' => $path,
                'parent_id' => $folderId,
            ]
        );

        if ($response->isError()) {
            throw new RequestException(
                $response->httpStatusCode(),
                'Create Folder request is not success. Details: ' . json_encode($response->error())
            );
        }

        $id = Arr::get($response->body(), 'id');

        if (strEmpty($id)) {
            throw new InvalidArgumentException('Folder id is empty');
        }

        return $id;
    }

    /**
     * @param string          $path
     * @param resource|string $content
     * @param bool            $overwrite
     *
     * @return Response
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RequestException
     * @throws RuntimeException
     */
    public function upload(string $path, $content, bool $overwrite = false): Response
    {
        $fileInfo = new FileInfo($path);

        $folderId = $this->createFolder($fileInfo->directories());

        return $this->_request()->post(
            "/storage/files?overwrite={$overwrite}",
            [
                'body' => $content,
                'headers' => [
                    'X-Kloudless-Metadata' => json_encode(['parent_id' => $folderId, 'name' => $fileInfo->filename()]),
                    'Content-Type' => 'application/octet-stream',
                ],
            ]
        );
    }

    /**
     * @param string $path
     * @param int    $size
     * @param bool   $overwrite
     *
     * @return Response
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RequestException
     * @throws RuntimeException
     */
    public function initMultipartSession(string $path, int $size, bool $overwrite = false): Response
    {
        $fileInfo = new FileInfo($path);

        return $this->_request()->postJson(
            "/storage/multipart?overwrite={$overwrite}",
            [
                'name' => $fileInfo->filename(),
                'parent_id' => $this->createFolder($fileInfo->directories()),
                'size' => $size,
            ]
        );
    }

    /**
     * @param string $id
     *
     * @return Response
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function abortMultipartSession(string $id): Response
    {
        return $this->_request()->delete("/storage/multipart/{$id}");
    }

    /**
     * @param string $id
     *
     * @return Response
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function completeMultipartSession(string $id): Response
    {
        return $this->_request()->post("/storage/multipart/{$id}/complete");
    }

    /**
     * @param string          $multipartId
     * @param resource|string $contents
     * @param int             $partNumber
     *
     * @return Response
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function uploadChunked(string $multipartId, $contents, int $partNumber): Response
    {
        return $this->_request()->put(
            "/storage/multipart/{$multipartId}?part_number={$partNumber}",
            [
                'body' => $contents,
                'headers' => [
                    'Content-Type' => 'application/octet-stream',
                ],
            ]
        );
    }

    /**
     * @param string $path
     * @param string $destination
     *
     * @return bool
     */
    public function move(string $path, string $destination): bool
    {
        return true;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function delete(string $path): bool
    {
        return true;
    }

    /**
     * @param string $path
     * @param string $destination
     *
     * @throws NotImplementedException
     */
    public function copy(string $path, string $destination): void
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @param string $path
     *
     * @return void
     * @throws NotImplementedException
     */
    public function download(string $path): void
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @param string $path
     *
     * @throws NotImplementedException
     */
    public function listFolder(string $path): void
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * Move function alias.
     */
    public function mv(): bool
    {
        return $this->move(...func_get_args());
    }

    /**
     * Delete function alias.
     */
    public function rm(): bool
    {
        return $this->delete(...func_get_args());
    }

    /**
     * @return string
     * @throws RequestException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function mkdir(): string
    {
        return $this->createFolder(...func_get_args());
    }

    /**
     * @return Request
     * @throws PropertyNotInit
     */
    private function _request(): Request
    {
        if (!$this->request instanceof Request) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->request;
    }
}
