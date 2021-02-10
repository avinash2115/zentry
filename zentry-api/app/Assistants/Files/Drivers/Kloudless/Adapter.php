<?php

namespace App\Assistants\Files\Drivers\Kloudless;

use App\Assistants\Files\Drivers\Contracts\Quotable;
use App\Assistants\Files\Drivers\Kloudless\Connection\Client;
use App\Assistants\Files\Drivers\Kloudless\Connection\Exception\RequestException;
use App\Assistants\Files\Drivers\ValueObjects\Quota;
use App\Convention\Contracts\File\Driver\HumanReadable;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Arr;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use InvalidArgumentException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;
use Log;
use RuntimeException;

/**
 * Class Adapter
 *
 * @package App\Assistants\Files\Drivers\Kloudless
 */
class Adapter extends AbstractAdapter implements HumanReadable, Quotable
{
    public const BYTES_LIMIT = 50000000;

    /**
     * @var Client
     */
    protected Client $client;

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @param Client $client
     * @param array  $options
     */
    public function __construct(Client $client, array $options = [])
    {
        $this->client = $client;
        $this->options = $options;
    }

    /**
     * @return Client
     * @throws PropertyNotInit
     */
    public function _client(): Client
    {
        if (!$this->client instanceof Client) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->client;
    }

    /**
     * @inheritDoc
     */
    public function write($path, $contents, Config $config)
    {
        return $this->upload($path, $contents, $config);
    }

    /**
     * @inheritDoc
     */
    public function update($path, $contents, Config $config)
    {
        return $this->upload($path, $contents, $config);
    }

    /**
     * @inheritDoc
     */
    public function rename($path, $newpath)
    {
        if (!$this->copy($path, $newpath)) {
            return false;
        }

        return $this->delete($path);
    }

    /**
     * @inheritDoc
     */
    public function delete($path)
    {
        return !$this->has($path);
    }

    /**
     * @inheritDoc
     */
    public function deleteDir($dirname)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function createDir($dirname, Config $config)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function has($path)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function read($path)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($path)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @inheritDoc
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @inheritDoc
     */
    public function writeStream($path, $resource, Config $config)
    {
        set_time_limit(0);

        $fstat = fstat($resource);

        if (!is_array($fstat)) {
            throw new InvalidArgumentException("Can't obtain file information");
        }

        $i = 0;

        $endByte = Arr::get($fstat, 'size', 0);

        if ($endByte <= self::BYTES_LIMIT) {
            return $this->upload($path, $resource, $config);
        }

        try {
            $response = $this->_client()->initMultipartSession($path, $endByte, true)->body();
        } catch (RequestException|PropertyNotInit|InvalidArgumentException|RuntimeException $e) {
            return false;
        }

        $multipartId = Arr::get($response, 'id');
        $partSize = (int) Arr::get($response, 'part_size');

        if ($partSize < $endByte) {
            $this->_client()->abortMultipartSession($multipartId);

            return $this->upload($path, $resource, $config);
        }

        $partNo = 1;

        while (!feof($resource) && $i <= $endByte) {
            $bytesToRead = $partSize;

            if (($i + $bytesToRead) > $endByte) {
                $bytesToRead = $endByte - $i + 1;
            }

            $data = fread($resource, (int) $bytesToRead);

            if (is_string($data)) {
                $this->_client()->uploadChunked($multipartId, $data, $partNo);
            }

            $i += $bytesToRead;
            $partNo++;
        }

        fclose($resource);

        return $this->normalizeResponse((array)$this->_client()->completeMultipartSession($multipartId));
    }

    /**
     * @inheritDoc
     */
    public function updateStream($path, $resource, Config $config)
    {
        // TODO change it after implementation has method
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * @inheritDoc
     */
    public function copy($path, $newpath)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function readStream($path)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @param string $path
     *
     * @return array|bool
     * @throws NotImplementedException
     */
    protected function readObject(string $path)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function setVisibility($path, $visibility)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function getVisibility($path)
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritdoc
     */
    public function applyPathPrefix($path)
    {
        return ltrim(parent::applyPathPrefix($path), '/');
    }

    /**
     * @inheritdoc
     */
    public function setPathPrefix($prefix)
    {
        $prefix = ltrim($prefix, '/');

        parent::setPathPrefix($prefix);
    }

    /**
     * @param string          $path
     * @param string|resource $content
     * @param Config          $config
     *
     * @return array|false
     * @throws RuntimeException
     */
    protected function upload(string $path, $content, Config $config)
    {
        try {
            $response = $this->_client()->upload($path, $content, true);
        } catch (Exception $exception) {

            if ($exception instanceof GuzzleRequestException) {
                $response = $exception->getResponse();
                Log::alert("Error while upload the file {$path}. Error: {$response->getBody()->getContents()}. Trace {$exception->getTraceAsString()}");
            } else {
                Log::alert("Error while upload the file {$path}. Error: {$exception->getMessage()}. Trace {$exception->getTraceAsString()}");
            }

            return false;
        }

        return $this->normalizeResponse($response->body());
    }

    /**
     * Check if the path contains only directories
     *
     * @param string $path
     *
     * @return bool
     */
    private function isOnlyDir($path)
    {
        return substr($path, -1) === DIRECTORY_SEPARATOR;
    }

    /**
     * Normalize the object result array.
     *
     * @param array  $response
     * @param string $path
     *
     * @return array
     */
    protected function normalizeResponse(array $response, $path = null)
    {
        return $response;
    }

    /**
     * @inheritDoc
     */
    public function quota(string $path = null): Quota
    {
        $response = $this->_client()->quota();
        $body = $response->body();

        return new Quota((int)Arr::get($body, 'used', 0), (int)Arr::get($body, 'total', ''));
    }
}
