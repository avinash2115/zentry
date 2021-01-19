<?php

namespace App\Assistants\Files\Drivers\Kloudless\Connection;

use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use \GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use Log;

/**
 * Class ApiClient
 *
 * @package App\Assistants\Files\Drivers\Kloudless
 */
class ApiClient
{
    private const DOMAIN = 'https://api.kloudless.com';

    private const VERSION = 'v1';

    private const BASE_URI = self::DOMAIN . '/' . self::VERSION;

    /**
     * @var GuzzleClient
     */
    protected GuzzleClient $guzzleClient;

    /**
     * @param string $accountId
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $accountId)
    {
        $apiKey = env('KLOUDLESS_API_KEY', '');

        if (strEmpty($apiKey)) {
            Log::error('ApiKey is missed.');

            throw new InvalidArgumentException('Custom storage is not configured properly');
        }

        if (strEmpty($accountId)) {
            Log::error("Account id can't be empty");

            throw new InvalidArgumentException('Custom storage is not configured properly');
        }

        $params = [
            'base_uri' => self::BASE_URI . "/accounts/{$accountId}/",
            'headers' => [
                'Authorization' => "ApiKey $apiKey",
            ]
        ];

        $this->guzzleClient = new GuzzleClient($params);
    }

    /**
     * @return GuzzleClient
     * @throws PropertyNotInit
     */
    public function client(): GuzzleClient
    {
        if (!$this->guzzleClient instanceof GuzzleClient) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->guzzleClient;
    }
}
