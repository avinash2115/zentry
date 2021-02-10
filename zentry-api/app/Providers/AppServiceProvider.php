<?php

namespace App\Providers;

use App\Assistants\Files\Drivers\AWS\S3\Adapter;
use App\Assistants\Files\Drivers\Local\Adapter as LocalAdapter;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Arr;
use Aws\S3\S3Client;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Storage;
use League\Flysystem\Adapter\Local as LeagueLocalAdapter;

/**
 * Class AppServiceProvider
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected string $modulePath;

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws InvalidFileException
     * @throws InvalidPathException
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $referer = request()->server('HTTP_REFERER');

        if (is_string($referer)) {
            $parts = parse_url($referer);

            if (is_array($parts) && Arr::has($parts, 'host')) {
                $envName = '.env.' . Arr::get($parts, 'host');

                if (file_exists(base_path($envName))) {
                    Dotenv::createMutable(base_path(), $envName)->load();

                    app()->make(LoadConfiguration::class)->bootstrap(app());
                }
            }
        }

        Storage::extend(
            's3',
            function ($app, $config) {
                $s3Config = $this->formatS3Config($config);

                return new Filesystem(new Adapter(new S3Client($s3Config), $s3Config['bucket'], $s3Config['root'] ?? null, $config['options'] ?? []));
            }
        );

        Storage::extend(
            'local',
            function ($app, $config) {

                $permissions = $config['permissions'] ?? [];

                $links = ($config['links'] ?? null) === 'skip'
                    ? LeagueLocalAdapter::SKIP_LINKS
                    : LeagueLocalAdapter::DISALLOW_LINKS;

                return new Filesystem(new LocalAdapter(
                    $config['root'], $config['lock'] ?? LOCK_EX, $links, $permissions
                ));
            }
        );
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function formatS3Config(array $config): array
    {
        $config += ['version' => 'latest'];

        if (! empty($config['key']) && ! empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }
}
