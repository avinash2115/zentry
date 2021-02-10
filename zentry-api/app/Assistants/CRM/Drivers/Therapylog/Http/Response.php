<?php

namespace App\Assistants\CRM\Drivers\Therapylog\Http;

use Exception;
use RuntimeException;

/**
 * Class Response
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\Http
 */
class Response
{
    /**
     * @var array
     */
    private array $body;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @var int
     */
    private int $httpStatusCode;

    /**
     * @var array
     */
    private array $errorMap = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        415 => 'Unsupported Media Type',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        596 => 'Service Not Found',
    ];

    /**
     * @param string $body
     * @param array  $headers
     * @param int    $httpStatusCode
     */
    public function __construct(string $body, array $headers, int $httpStatusCode)
    {
        $body = json_decode($body, true);

        $this->body = $body ?? [];
        $this->headers = $headers;
        $this->httpStatusCode = $httpStatusCode;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->httpStatusCode >= 400;
    }

    /**
     * @return array
     */
    public function body(): array
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function httpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * @return array
     * @throws Exception|RuntimeException
     */
    public function error(): array
    {
        if ($this->httpStatusCode() >= 200 && $this->httpStatusCode() < 300) {
            throw new RuntimeException('Request was successful, there are no error details');
        }

        return [
            'raw_body' => $this->body,
            'json_body' => $this->body(),
            'status' => [
                'code' => $this->httpStatusCode,
                'text' => (array_key_exists($this->httpStatusCode, $this->errorMap)) ? $this->errorMap[$this->httpStatusCode] : 'Unknown Error',
            ],
        ];
    }
}
