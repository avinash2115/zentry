<?php

namespace Tests;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Session\Repository\SessionRepositoryContract;
use App\Components\Sessions\Session\Repository\SessionRepositoryDoctrine;
use App\Components\Sessions\Session\Transcription\Repository\TranscriptionRepositoryODM;
use App\Components\Sessions\Session\Transcription\TranscriptionReadonlyContract;
use App\Components\Users\Device\Repository\DeviceRepositoryContract;
use App\Components\Users\Device\Repository\DeviceRepositoryDoctrine;
use App\Components\Users\PasswordReset\Repository\PasswordResetRepositoryContract;
use App\Components\Users\PasswordReset\Repository\PasswordResetRepositoryDoctrine;
use App\Components\Users\User\Repository\UserRepositoryContract;
use App\Components\Users\User\Repository\UserRepositoryDoctrine;
use App\Components\Users\User\UserContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Convention\Tests\Traits\HelperTrait;
use Auth;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

/**
 * Class IntegrationTestCase
 *
 * @package Tests
 */
abstract class IntegrationTestCase extends TestCase
{
    use HelperTrait;
    use RefreshDatabase;

    /**
     * Additional headers for the request.
     *
     * @var array
     */
    protected $defaultHeaders = [
        'Accept' => 'application/vnd.api+json',
        'Content-Type' => 'application/vnd.api+json',
    ];

    /**
     * @var string
     */
    protected string $email = 'echo@test.com';

    /**
     * @var string
     */
    protected string $password = 'ECHOORIGINALPASSWORD';

    /**
     * @var string|null
     */
    protected ?string $authToken = null;

    /**
     * @throws BindingResolutionException|NonUniqueResultException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(
            UserRepositoryContract::class,
            function () {
                return $this->app->make(UserRepositoryDoctrine::class);
            }
        );
        $this->app->singleton(
            PasswordResetRepositoryContract::class,
            function () {
                return $this->app->make(PasswordResetRepositoryDoctrine::class);
            }
        );
        $this->app->singleton(
            DeviceRepositoryContract::class,
            function () {
                return $this->app->make(DeviceRepositoryDoctrine::class);
            }
        );
        $this->app->singleton(
            SessionRepositoryContract::class,
            function () {
                return $this->app->make(SessionRepositoryDoctrine::class);
            }
        );
        $this->app->singleton(
            TranscriptionReadonlyContract::class,
            function () {
                return $this->app->make(TranscriptionRepositoryODM::class);
            }
        );
    }

    /**
     * @return UserContract|null
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     */
    public function getUser()
    {
        return $this->userRepository()->filterByEmails([$this->email])->getOne();
    }

    protected function tearDown(): void
    {
        $this->artisan('migrate:fresh');

        parent::tearDown();
    }

    /**
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     */
    protected function setToken(): void
    {
        $user = $this->getUser();

        if (!$user instanceof UserContract) {
            $this->userRepository()->persist(
                $this->createUser(new Email($this->email), new HashedPassword($this->password))
            );
            $this->flush();
        }

        $this->authToken = Auth::attempt(
            (new Credentials(new Email($this->email), new HashedPassword($this->password)))->toArray()
        );
    }

    /**
     * @param string $type
     * @param array  $attributes
     * @param array  $relationships
     * @param string $id
     *
     * @return array
     */
    protected function asData(string $type, array $attributes = [], array $relationships = [], string $id = ''): array
    {
        return [
            'data' => [
                'type' => $type,
                'id' => $id,
                'attributes' => $attributes,
                'relationships' => $relationships,
            ],
        ];
    }

    /**
     * @return array
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     */
    protected function authHeader(): array
    {
        if ($this->authToken === null) {
            $this->setToken();
        }

        return [
            'Authorization' => 'Bearer ' . $this->authToken,
        ];
    }

    /**
     * Define additional headers to be sent with the request.
     *
     * @return IntegrationTestCase
     * @throws BindingResolutionException
     * @throws NonUniqueResultException
     */
    public function withAuthHeader(): IntegrationTestCase
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $this->authHeader());

        return $this;
    }

    /**
     * @return IntegrationTestCase
     */
    public function withDeleteHeader(): IntegrationTestCase
    {
        $this->defaultHeaders = array_merge(
            $this->defaultHeaders,
            [
                'X-HTTP-METHOD-OVERRIDE' => 'DELETE',
            ]
        );

        return $this;
    }

    /**
     * @param TestResponse $response
     *
     * @return JsonApi
     */
    protected function asJsonApi(TestResponse $response): JsonApi
    {
        return new JsonApi(collect(json_decode($response->content(), true, 512, JSON_THROW_ON_ERROR)));
    }

    /**
     * @inheritDoc
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        $response = parent::json($method, $uri, $data, $headers);

        $this->setDefaultHeaders();

        return $response;
    }

    /**
     *
     */
    private function setDefaultHeaders(): void
    {
        $this->defaultHeaders = [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ];
    }
}
