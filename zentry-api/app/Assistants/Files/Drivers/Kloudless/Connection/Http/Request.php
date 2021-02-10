<?php

namespace App\Assistants\Files\Drivers\Kloudless\Connection\Http;

use App\Assistants\Files\Drivers\Kloudless\Connection\ApiClient;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Class Request
 *
 * @package App\Assistants\Files\Drivers\Kloudless\Connection\Http
 */
class Request
{
    /**
     * @var ApiClient
     */
    private ApiClient $apiClient;

    /**
     * Request constructor.
     *
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @param string $url
     *
     * @return Response
     * @throws RuntimeException
     */
    public function get(string $url): Response
    {
        return $this->makeResponse($this->_apiClient()->client()->request('GET', $this->normalizeUrl($url)));
    }

    /**
     * Process curl POST request.
     *
     * @param string $url
     * @param array  $data
     *
     * @return Response
     * @throws RuntimeException
     */
    public function post(string $url, array $data = []): Response
    {
        return $this->makeResponse($this->_apiClient()->client()->request('POST', $this->normalizeUrl($url), $data));
    }

    /**
     * @param string $url
     * @param array  $json
     *
     * @return Response
     * @throws RuntimeException
     */
    public function postJson(string $url, array $json = []): Response
    {
        return $this->post($url, ['json' => $json]);
    }

    /**
     * @param string $url
     * @param array  $data
     *
     * @return Response
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    public function put(string $url, array $data = []): Response
    {
        return $this->makeResponse($this->_apiClient()->client()->request('PUT', $this->normalizeUrl($url), $data));
    }

    /**
     * Process curl DELETE request.
     *
     * @param string $url
     *
     * @return Response
     * @throws RuntimeException
     */
    public function delete(string $url)
    {
        return $this->makeResponse($this->_apiClient()->client()->request('DELETE', $this->normalizeUrl($url)));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Response
     * @throws RuntimeException
     */
    private function makeResponse(ResponseInterface $response): Response
    {
        return new Response(
            $response->getBody()->getContents(), $response->getHeaders(), $response->getStatusCode()
        );
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function normalizeUrl(string $url): string
    {
        return ltrim($url, DIRECTORY_SEPARATOR);
    }

    /**
     * @return ApiClient
     * @throws PropertyNotInit
     */
    public function _apiClient(): ApiClient
    {
        if (!$this->apiClient instanceof ApiClient) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->apiClient;
    }
}
