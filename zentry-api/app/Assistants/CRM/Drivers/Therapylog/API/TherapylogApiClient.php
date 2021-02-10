<?php

namespace App\Assistants\CRM\Drivers\Therapylog\API;

use App\Assistants\CRM\Drivers\Therapylog\Http\Response;
use App\Assistants\CRM\Exceptions\ConnectionFailed;
use App\Assistants\CRM\Exceptions\InvalidCredentials;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

/**
 * Class TherapylogApiClient
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\API
 */
class TherapylogApiClient
{
    private const PREFIX = 'api';

    private const VERSION = 'v1';

    /**
     * @var GuzzleClient
     */
    protected GuzzleClient $guzzleClient;

    /**
     * TherapylogApiClient constructor.
     */
    public function __construct()
    {
        $params = [
            'base_uri' => config('crm.therapylog.url') . '/' . self::PREFIX . '/' . self::VERSION . '/',
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $this->guzzleClient = new GuzzleClient($params);
    }

    /**
     * @return GuzzleClient
     * @throws PropertyNotInit
     */
    private function client(): GuzzleClient
    {
        if (!$this->guzzleClient instanceof GuzzleClient) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->guzzleClient;
    }

    /**
     * @param string $token
     * @param string $uri
     * @param string $method
     * @param array  $data
     *
     * @return Response
     * @throws InvalidCredentials|ConnectionFailed
     */
    public function sendPrivateRequest(string $token, string $uri, string $method = 'GET', array $data = []): Response
    {
        return $this->_sendRequest($uri, $method, $data, ['Authorization' => 'Token ' . $token]);
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array  $data
     *
     * @return Response
     * @throws InvalidCredentials|ConnectionFailed
     */
    public function sendPublicRequest(string $uri, string $method = 'GET', array $data = []): Response
    {
        return $this->_sendRequest($uri, $method, $data, []);
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array  $data
     * @param array  $headers
     *
     * @return Response
     * @throws InvalidCredentials|ConnectionFailed
     */
    private function _sendRequest(string $uri, string $method, array $data, array $headers): Response
    {
        //@todo need refactoring
        try {
            //            if ($method === 'GET') {
            //
            //            }
            $response = $this->client()->request(
                $method,
                $uri,
                [
                    'form_params' => $data,
                    'headers' => $headers,
                ]
            );

            return $this->prepareResponse($response);
        } catch (Exception $exception) {
            switch ($exception->getCode()) {
                case 401:
                    throw new InvalidCredentials();
                default:
                    throw new ConnectionFailed($exception->getMessage());
            }
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Response
     */
    private function prepareResponse(ResponseInterface $response): Response
    {
        return new Response(
            $response->getBody()->getContents(), $response->getHeaders(), $response->getStatusCode()
        );
    }
}
