<?php

namespace App\Assistants\Files\Services\Stream\Video;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\LocalFileServiceTrait;
use App\Assistants\Files\ValueObjects\Metadata;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnexpectedValueException;

/**
 * Class Stream
 *
 * @package App\Assistants\Files\Services\Stream\Video
 */
class Stream
{
    use FileServiceTrait;
    use LocalFileServiceTrait;

    public const BUFFER_SIZE = 102400;

    /**
     * @var resource
     */
    private $stream;

    /**
     * @var string
     */
    private string $mimeType;

    /**
     * @var Metadata|null
     */
    private ?Metadata $metadata = null;

    /**
     * @var int
     */
    private int $startByte = -1;

    /**
     * @var int
     */
    private int $endByte = -1;

    /**
     * Start streaming video content
     *
     * @param string $filePath
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws FileNotFoundException
     * @throws PropertyNotInit
     * @throws ReadException
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function play(string $filePath): Response
    {
        if ($this->fileService__()->isExist($filePath)) {
            $service = $this->fileService__();
        } elseif ($this->localFileService__()->isExist($filePath)) {
            $service = $this->localFileService__();
        } else {
            throw new NotFoundException();
        }

        $this->stream = $service->asResource($filePath);
        $this->mimeType = $service->mimeType($filePath);
        $this->metadata = $service->metadata($filePath);

        $this->startByte = 0;
        $this->endByte = $this->_metadata()->size() - 1;

        try {
            return new StreamedResponse(
                function () {
                    $this->stream();
                }, 206, $this->_headers()
            );
        } catch (UnexpectedValueException $exception) {
            return response('Requested Range Not Satisfiable', 416);
        }
    }

    /**
     * @return array|string[]
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    private function _headers(): array
    {
        $headers = [
            'Content-Type' => $this->_mimeType(),
            'Cache-Control' => 'max-age=2592000, public',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT',
            'Last-Modified' => gmdate('D, d M Y H:i:s', $this->_metadata()->timestamp()) . ' GMT',
            'Accept-Ranges' => "0-{$this->_endByte()}",
        ];

        $serverArgs = request()->server();

        if (is_array($serverArgs) && Arr::has($serverArgs, 'HTTP_RANGE')) {
            [, $range] = explode('=', Arr::get($serverArgs, 'HTTP_RANGE'), 2);

            if (strpos($range, ',') !== false) {
                throw new UnexpectedValueException();
            }

            $endByte = $this->_endByte();

            if ($range === '-') {
                $startByte = $this->_metadata()->size() - (int)substr($range, 1);
            } else {
                $range = explode('-', $range);
                $startByte = $range[0];

                $endByte = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $this->_endByte();
            }

            $endByte = ($endByte > $this->_endByte()) ? $this->endByte : $endByte;

            if ($startByte > $endByte || $endByte >= $this->_metadata()->size() || $startByte > $this->_metadata()
                    ->size() - 1) {
                throw new UnexpectedValueException();
            }

            $this->startByte = (int)$startByte;
            $this->endByte = (int)$endByte;

            $length = $this->_endByte() - $this->_startByte() + 1;

            fseek($this->_stream(), $this->_startByte());

            Arr::set($headers, 'Content-Length', $length);
            Arr::set(
                $headers,
                'Content-Range',
                "bytes {$this->_startByte()}-{$this->_endByte()}/{$this->_metadata()->size()}"
            );
        } else {
            Arr::set($headers, 'Content-Length', $this->_metadata()->size());
        }

        return $headers;
    }

    /**
     * @return Stream
     * @throws UnexpectedValueException
     */
    private function stream(): Stream
    {
        $i = $this->_startByte();

        set_time_limit(0);

        $fh = fopen('php://output', 'wb');

        if (is_resource($fh)) {
            while (!feof($this->_stream()) && $i <= $this->_endByte()) {
                $bytesToRead = self::BUFFER_SIZE;

                if (($i + $bytesToRead) > $this->_endByte()) {
                    $bytesToRead = $this->_endByte() - $i + 1;
                }

                $data = fread($this->_stream(), $bytesToRead);

                if (is_string($data)) {
                    fwrite($fh, $data);
                }

                $i += $bytesToRead;
            }
        }

        return $this;
    }

    /**
     * @return resource
     * @throws UnexpectedValueException
     */
    private function _stream()
    {
        if (!is_resource($this->stream)) {
            throw new UnexpectedValueException('Stream must be instance of resource.');
        }

        return $this->stream;
    }

    /**
     * @return string
     * @throws PropertyNotInit
     */
    private function _mimeType(): string
    {
        if (!is_string($this->mimeType) || strEmpty($this->mimeType)) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->mimeType;
    }

    /**
     * @return Metadata
     * @throws PropertyNotInit
     */
    private function _metadata(): Metadata
    {
        if (!$this->metadata instanceof Metadata) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->metadata;
    }

    /**
     * @return int
     */
    private function _startByte(): int
    {
        return $this->startByte;
    }

    /**
     * @return int
     */
    private function _endByte(): int
    {
        return $this->endByte;
    }
}
