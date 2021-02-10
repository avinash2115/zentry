<?php

namespace App\Assistants\CRM\Drivers\Therapylog;

use App\Assistants\CRM\Drivers\Therapylog\API\TherapylogApiClient;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Meta;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Records;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Caseload\Caseload;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\CategoryMapping;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Goal\Records as GoalRecords;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\IEP\Records as IEPRecords;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Provider;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Service;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Providers;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceTransaction;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ProviderTransaction;
use App\Assistants\CRM\Exceptions\ConnectionFailed;
use App\Assistants\CRM\Exceptions\InvalidCredentials;
use Arr;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class TherapylogClient
 *
 * @package App\Assistants\CRM\Drivers\Therapylog
 */
class TherapylogClient
{
    public const PER_PAGE = 100;

    /**
     * @var TherapylogApiClient|null
     */
    private ?TherapylogApiClient $api = null;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var string|null
     */
    private ?string $latestAuthToken = null;

    /**
     * TherapylogClient constructor.
     *
     * @param string $email
     * @param string $password
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $email, string $password)
    {
        if (strEmpty($email)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email is not valid');
        }

        $this->email = $email;

        if (strEmpty($password)) {
            throw new InvalidArgumentException('Password cannot be empty');
        }

        $this->password = $password;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return TherapylogApiClient
     * @throws BindingResolutionException
     */
    private function client(): TherapylogApiClient
    {
        if (!$this->api instanceof TherapylogApiClient) {
            $this->api = app()->make(TherapylogApiClient::class);
        }

        return $this->api;
    }

    /**
     * @return Collection|CategoryMapping[]
     * @throws BindingResolutionException|InvalidCredentials|ConnectionFailed
     */
    public function categoryMapping(): Collection
    {
        return collect($this->client()->sendPrivateRequest($this->login(), 'category_mapping')->body())->map(
            fn($item) => new CategoryMapping(
                Arr::get($item, 'category', ''), Arr::get($item, 'roles', []), Arr::get($item, 'models', [])
            )
        );
    }

    /**
     * @return Provider
     * @throws BindingResolutionException|InvalidCredentials|ConnectionFailed
     */
    public function provider(): Provider
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            'provider',
            'GET',
            [
                'include_member_districts' => 1,
            ]
        )->body();

        return new Provider(Arr::get($result, 'id'), Arr::get($result, 'districts', []));
    }

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function services(): Collection
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            'services'
        )->body();

        return collect($result['services'])->map(
            static function (array $item) {
                return new Service($item);
            }
        );
    }

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function providers(): Collection
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            'providers'
        )->body();

        return collect($result['providers'])->map(
            static function (array $item) {
                return new Provider($item);
            }
        );
    }

    /**
     * @param int $districtId
     * @param int $page
     * @param int $perPage
     *
     * @return Records
     * @throws BindingResolutionException|InvalidCredentials|ConnectionFailed
     */
    public function caseloadRecords(int $districtId = null, int $page = 1, int $perPage = self::PER_PAGE): Records
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            'caseload_records',
            'GET',
            [
                'district_id' => $districtId,
                'page' => $page,
                'per_page' => $perPage,
            ]
        )->body();

        return new Records(
            collect($result['caseload_records'])->map(
                static function (array $record) {
                    return new Caseload($record);
                }
            ), new Meta($result['meta'])
        );
    }

    /**
     * @param mixed $fromDate
     * @param mixed $toDate
     * @param int   $page
     * @param int   $perPage
     * @param int   $documented
     * @param int   $lite
     *
     * @return Records
     * @throws BindingResolutionException|InvalidCredentials|ConnectionFailed
     */
    public function serviceTransactionRecords(
        $fromDate,
        $toDate,
        int $page = 1,
        int $perPage = self::PER_PAGE,
        $documented = 0,
        $lite = 0
    ): Records {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            'service_transactions',
            'GET',
            [
                'from' => $fromDate,
                'to' => $toDate,
                'page' => $page,
                'per_page' => $perPage,
                'documented' => $documented,
                'lite' => $lite,
            ]
        )->body();

        return new Records(
            collect($result['service_transactions'])->map(
                static function (array $record) {
                    return new ServiceTransaction($record);
                }
            ), new Meta($result['meta'])
        );
    }

    /**
     * @param mixed $fromDate
     * @param mixed $toDate
     * @param int   $page
     * @param int   $perPage
     * @param int   $documented
     * @param int   $lite
     *
     * @return Records
     * @throws BindingResolutionException|InvalidCredentials|ConnectionFailed
     */
    public function providerTransactionRecords(
        $fromDate,
        $toDate,
        int $page = 1,
        int $perPage = self::PER_PAGE,
        $documented = 0,
        $lite = 0
    ): Records {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            'provider_transactions',
            'GET',
            [
                'from' => $fromDate,
                'to' => $toDate,
                'page' => $page,
                'per_page' => $perPage,
                'documented' => $documented,
                'lite' => $lite,
            ]
        )->body();

        return new Records(
            collect($result['provider_transactions'])->map(
                static function (array $record) {
                    return new ProviderTransaction($record);
                }
            ), new Meta($result['meta'])
        );
    }

    /**
     * @param int $studentId
     *
     * @return GoalRecords
     * @throws BindingResolutionException
     */
    public function goals(int $studentId): GoalRecords
    {
        $result = $this->client()->sendPrivateRequest(
            $this->tokenOrLogin(),
            'goals',
            'GET',
            [
                'student_id' => $studentId,
            ]
        )->body();

        return new GoalRecords($result['goals']);
    }

    /**
     * @param int $studentId
     *
     * @return IEPRecords
     * @throws BindingResolutionException
     */
    public function ieps(int $studentId): IEPRecords
    {
        $result = $this->client()->sendPrivateRequest(
            $this->tokenOrLogin(),
            'ieps',
            'GET',
            [
                'student_id' => $studentId,
            ]
        )->body();

        return new IEPRecords($result['ieps']);
    }

    /**
     * @return string
     * @throws BindingResolutionException|InvalidCredentials|ConnectionFailed
     */
    public function login(): string
    {
        $response = $this->client()->sendPublicRequest(
            'sessions',
            'POST',
            [
                'email' => $this->email,
                'password' => $this->password,
            ]
        );

        $token = Arr::get($response->body(), 'token');

        if (strEmpty($token)) {
            throw new InvalidArgumentException('Token is empty');
        }

        return $token;
    }

    /**
     * @return string
     * @throws BindingResolutionException
     */
    private function tokenOrLogin(): string
    {
        if (!is_string($this->latestAuthToken)) {
            $this->latestAuthToken = $this->login();
        }

        return $this->latestAuthToken;
    }

    /**
     * @param array $data
     *
     * @return ServiceTransaction
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function createServiceTransaction(array $data): ServiceTransaction
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            'service_transactions',
            'POST',
            $data
        )->body();

        return new ServiceTransaction($result);
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return ServiceTransaction
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function updateServiceTransaction(int $id, array $data): ServiceTransaction
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            "service_transactions/{$id}",
            'PUT',
            $data
        )->body();

        return new ServiceTransaction($result);
    }

    /**
     * @param int $id
     *
     * @return ServiceTransaction
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function serviceTransaction(int $id): ServiceTransaction
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            "service_transactions/{$id}",
        )->body();

        return new ServiceTransaction($result);
    }

     /**
     * @param array $data
     *
     * @return ProviderTransaction
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function createProviderTransaction(array $data): ProviderTransaction
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            'provider_transactions',
            'POST',
            $data
        )->body();

        return new ProviderTransaction($result);
    }

    /**
     * @param int   $id
     * @param array $data
     *
     * @return ProviderTransaction
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function updateProviderTransaction(int $id, array $data): ProviderTransaction
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            "provider_transactions/{$id}",
            'PUT',
            $data
        )->body();

        return new ProviderTransaction($result);
    }

    /**
     * @param int $id
     *
     * @return ProviderTransaction
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function providerTransaction(int $id): ProviderTransaction
    {
        $result = $this->client()->sendPrivateRequest(
            $this->login(),
            "provider_transactions/{$id}",
        )->body();

        return new ProviderTransaction($result);
    }
}

