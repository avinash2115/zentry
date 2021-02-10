<?php

namespace App\Components\Sessions\Services\Poi\Indexable;

use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Traits\IndexableTrait;
use App\Assistants\Elastic\ValueObjects\Body;
use App\Assistants\Elastic\ValueObjects\Document;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Components\Sessions\Services\Poi\PoiServiceContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Tag;

/**
 * Class IndexableService
 *
 * @package App\Components\Sessions\Services\Poi\Indexable
 */
class IndexableService extends SetupService implements IndexableServiceContract
{
    use IndexableTrait;

    /**
     * @var SessionReadonlyContract
     */
    private SessionReadonlyContract $session;

    /**
     * @var PoiServiceContract
     */
    private PoiServiceContract $poiService;

    /**
     * @param SessionReadonlyContract $session
     * @param PoiServiceContract      $poiService
     */
    public function __construct(SessionReadonlyContract $session, PoiServiceContract $poiService)
    {
        $this->session = $session;
        $this->poiService = $poiService;
    }

    /**
     * @inheritDoc
     */
    public function asIdentity(): Identity
    {
        return $this->_poiService()->identity();
    }

    /**
     * @inheritDoc
     */
    public function asDocument(Index $index): Document
    {
        switch ($index->index()) {
            case Index::INDEX_ENTITIES:
                $data = collect(
                    [
                        'session_id' => $this->_session()->identity()->toString(),
                        'user_id' => $this->_session()->user()->identity()->toString(),
                        'name' => $this->_poiService()->readonly()->name(),
                        'words' => collect(
                            $this->_poiService()->wordsSimplified()->get($this->_poiService()->identity()->toString())
                        )->implode(' '),
                        'tags' => $this->_poiService()->readonly()->tags()->tags()->map(function(Tag $tag) {
                            return $tag->tag();
                        })->values()->toArray(),
                    ]
                );
            break;
            default:
                throw new IndexNotSupported($index);
        }

        return new Document(
            $index,
            $this->asType(),
            $this->_poiService()->identity(),
            new Body($data->toArray(), $this->asMappings($index))
        );
    }

    /**
     * @return PoiServiceContract
     * @throws PropertyNotInit
     */
    private function _poiService(): PoiServiceContract
    {
        if (!$this->poiService instanceof PoiServiceContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->poiService;
    }

    /**
     * @return SessionReadonlyContract
     * @throws PropertyNotInit
     */
    private function _session(): SessionReadonlyContract
    {
        if (!$this->session instanceof SessionReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->session;
    }
}
