<?php

namespace App\Components\Users\Console\Elastic\Indexing\Participant;

use App\Assistants\Elastic\Console\Generators\Base\Traits\IndexableTrait;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Console\Command;
use Flusher;

/**
 * Class Index
 *
 * @package App\Components\Users\Console\Elastic\Indexing\Participant
 */
class Index extends Command
{
    use ParticipantServiceTrait;
    use IndexableTrait;

    public const SIGNATURE = 'generators:users:participants:elastic:index';

    /**
     * @var string
     */
    protected $signature = self::SIGNATURE . self::OPTION_MEMORY;

    /**
     * @var string
     */
    protected $description = 'Generate users participants elastic indexes';

    /**
     *
     */
    public function handle(): void
    {
        $this->_handle(function () {
            $items = $this->participantService__()->listRO();

            $this->progressStart($items->count());

            $items->each(function (ParticipantReadonlyContract $session) {
                $this->index($this->participantService__()->workWith($session->identity()));

                Flusher::clear();

                $this->progressAdvance();
                $this->memoryConsumption();
            });

            $this->progressFinish();
        });
    }
}
