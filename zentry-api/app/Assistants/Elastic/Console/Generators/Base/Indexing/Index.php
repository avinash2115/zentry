<?php

namespace App\Assistants\Elastic\Console\Generators\Base\Indexing;

use App\Assistants\Elastic\Console\Generators\Filter\Setup;
use App\Assistants\Search\Services\SearchService;
use App\Components\Sessions\Console\Elastic\Indexing\Index as SessionIndex;
use App\Components\Users\Console\Elastic\Indexing\Participant\Index as UsersParticipantsIndex;
use App\Console\Command;
use Elasticsearch\ClientBuilder;

/**
 * Class Index
 *
 * @package App\Console\Generators\Base\Elastic\Indexing
 */
class Index extends Command
{
    public const SIGNATURE = 'generators:elastic:index';

    /**
     * @var string
     */
    protected $signature = self::SIGNATURE . self::OPTION_MEMORY;

    /**
     * @var string
     */
    protected $description = 'Generate elastic indexes';

    /**
     *
     */
    public function handle(): void
    {
        $arguments = collect();

        if ($this->option('memory')) {
            $arguments->put('--memory', true);
        }

        $arguments = $arguments->toArray();

        $client = ClientBuilder::create()->setHosts([
            config('elasticsearch.host') . ":" . config('elasticsearch.port'),
        ])->build();

        $client->indices()->delete(['index' => env('APP_ENV') . "*"]);

        $this->call(Setup::SIGNATURE, $arguments);

        $this->_handle(function () {
            app()->make(SearchService::class)->setup();
        });

        $this->call(UsersParticipantsIndex::SIGNATURE, $arguments);
        $this->call(SessionIndex::SIGNATURE, $arguments);
    }
}
