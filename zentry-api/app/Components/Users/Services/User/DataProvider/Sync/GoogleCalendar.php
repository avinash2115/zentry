<?php

namespace App\Components\Users\Services\User\DataProvider\Sync;

use App\Components\Users\Exceptions\DataProvider\Auth\TokenExpired;
use App\Components\Users\Exceptions\DataProvider\ServiceUnavailableException;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use App\Components\Users\ValueObjects\DataProvider\Sync\Event;
use App\Components\Users\ValueObjects\DataProvider\Sync\Participant;
use App\Convention\ValueObjects\Config\Config;
use App\Convention\ValueObjects\Config\Option;
use Arr;
use Carbon\Carbon;
use Exception;
use Google_Client;
use Google_Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventAttendee;
use Google_Service_Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Log;
use LogicException;

/**
 * Class GoogleCalendar
 *
 * @package App\Components\Users\Services\User\DataProvider\Sync
 */
class GoogleCalendar
{
    public const CALENDAR_ID = 'primary';

    public const CONFIG_KEY_CREDENTIALS = 'credentials';

    public const CONFIG_KEY_REDIRECT_URI = 'redirect_uri';

    /**
     * @var string
     */
    private string $code;

    /**
     * @var array|null
     */
    private ?array $accessToken = null;

    /**
     * @var Google_Client|null
     */
    private ?Google_Client $client = null;

    /**
     * GoogleCalendar constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $code = $config->options()->first(
            fn(Option $option) => $option->isType(DataProviderReadonlyContract::CONFIG_AUTH_CODE_KEY)
        );
        $accessToken = $config->options()->first(
            fn(Option $option) => $option->isType(
                    DataProviderReadonlyContract::CONFIG_ACCESS_TOKEN_KEY
                ) && $option->value() !== ''
        );

        $this->code = $code instanceof Option ? $code->value() : '';

        if ($accessToken instanceof Option) {
            $unserialized = unserialize($accessToken->value());
            if (is_array($unserialized)) {
                $this->accessToken = $unserialized;
            }
        }
    }

    /**
     * @return Collection
     * @throws TokenExpired
     * @throws ServiceUnavailableException
     */
    public function events(): Collection
    {
        $client = $this->_client();
        $service = new Google_Service_Calendar($client);

        $params = [
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => Carbon::now()->subHour()->format('c'),
            'timeMax' => Carbon::now()->addMonth()->format('c'),
        ];

        try {
            return collect($service->events->listEvents(self::CALENDAR_ID, $params)->getItems())->filter(
                fn(Google_Service_Calendar_Event $event) => $event->getStart()->getDateTime(
                    ) !== null && $event->getEnd()->getDateTime() !== null
            )->map(
                static function (Google_Service_Calendar_Event $event) {
                    try {
                        return new Event(
                            $event->getSummary(),
                            $event->getId(),
                            $event->getStart()->getDateTime(),
                            $event->getEnd()->getDateTime(),
                            collect($event->getAttendees())->map(
                                static function (Google_Service_Calendar_EventAttendee $attendee) {
                                    return new Participant($attendee->getEmail(), $attendee->getDisplayName());
                                }
                            )->filter(
                                static function (Participant $participant) use ($event) {
                                    return $participant->email() !== $event->getCreator()->email;
                                }
                            )->toArray(),
                            $event->getDescription(),
                        );
                    } catch (Exception $exception) {
                        report($exception);

                        return null;
                    }
                }
            )->filter();
        } catch (Google_Service_Exception $exception) {
            throw new ServiceUnavailableException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * Returns an authorized API client.
     *
     * @return Google_Client the authorized client object
     * @throws Exception
     * @throws TokenExpired
     */
    private function _client(): Google_Client
    {
        if (!$this->client instanceof Google_Client) {
            $this->login();
        }

        return $this->client;
    }

    /**
     * @return GoogleCalendar
     * @throws Google_Exception
     * @throws InvalidArgumentException
     * @throws TokenExpired
     */
    public function login(): GoogleCalendar
    {
        $client = new Google_Client();
        $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);

        $client->setAuthConfig(
            config(
                'users.data_providers.' . DataProviderReadonlyContract::DRIVER_GOOGLE_CALENDAR . '.' . self::CONFIG_KEY_CREDENTIALS
            )
        );
        $client->setAccessType('offline');
        $client->setRedirectUri('postmessage');

        if (is_array($this->accessToken)) {
            $client->setAccessToken($this->accessToken);
        } else {
            $accessToken = $client->fetchAccessTokenWithAuthCode($this->code);

            if (Arr::has($accessToken, 'error')) {
                $error = 'Fetch token with code error : ' . Arr::get($accessToken, 'error');
                Log::info($error);

                throw new TokenExpired($error);
            }
        }

        // If there is no previous token or it's expired.
        // Refresh the token if possible, else fetch a new one.
        if (is_array($this->accessToken()) && $client->isAccessTokenExpired()) {
            if ($client->getRefreshToken() !== null) {
                try {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    $this->accessToken = $client->getAccessToken();
                } catch (Exception $exception) {
                    $client->revokeToken();

                    throw new TokenExpired();
                }
            } else {
                $client->revokeToken();

                throw new TokenExpired();
            }
        }

        $this->setAccessToken($client->getAccessToken());

        $this->client = $client;

        return $this;
    }

    /**
     * @return array|null
     */
    public function accessToken(): ?array
    {
        return $this->accessToken;
    }

    /**
     * @throws Google_Exception
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws TokenExpired
     */
    public function revokeToken(): void
    {
        $this->_client()->revokeToken();
    }

    /**
     * @param array $data
     */
    private function setAccessToken(array $data): void
    {
        $this->accessToken = $this->accessToken() === null ? $data : array_merge($this->accessToken(), $data);
    }
}
