<?php

namespace App\Components\Sessions\Console\Elastic\Indexing;

use App\Assistants\Elastic\Console\Generators\Base\Traits\IndexableTrait;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Console\Command;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Flusher;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class Index
 *
 * @package App\Components\Sessions\Console\Elastic\Indexing
 */
class Index extends Command
{
    use SessionServiceTrait;
    use IndexableTrait;

    public const SIGNATURE = 'generators:session:elastic:index';

    /**
     * @var string
     */
    protected $signature = self::SIGNATURE . self::OPTION_MEMORY;

    /**
     * @var string
     */
    protected $description = 'Generate sessions elastic indexes';

    /**
     *
     */
    public function handle(): void
    {
        $this->_handle(
            function () {
                $this->progressStart($this->sessionService__()->count());

                $this->processBatch();

                $this->progressFinish();
            }
        );
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return bool
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    private function processBatch(int $offset = 0, int $limit = 10): bool
    {
        $this->sessionService__()->applyFilters(
            [
                'limit' => $limit,
                'offset' => $offset,
            ]
        );

        if ($this->sessionService__()->listRO()->each(
            function (SessionReadonlyContract $entity) {
                $this->index($this->sessionService__()->workWith($entity->identity()));
                $this->indexPois($entity);

                Flusher::clear();

                $this->progressAdvance();
                $this->memoryConsumption();
            }
        )->isNotEmpty()) {
            $this->processBatch($offset + $limit, $limit);

            Flusher::clear();
        }

        return true;
    }

    /**
     * @param SessionReadonlyContract $entity
     */
    private function indexPois(SessionReadonlyContract $entity): void
    {
        $entity->pois()->each(
            function (PoiReadonlyContract $entity) {
                $this->index($this->sessionService__()->poiService()->workWith($entity->identity())->indexableService());
            }
        );
    }
}
