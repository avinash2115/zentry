<?php

namespace App\Assistants\Elastic\Services\Setup;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\Contracts\Indexable\SetupableContract;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Services\ElasticServiceContract;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mapping;
use Illuminate\Support\Collection;

/**
 * Class FilterService
 *
 * @package App\Assistants\Elastic\Services\Setup
 */
class FilterService implements FilterServiceContract
{
    use ElasticServiceTrait;

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        $index = $this->elasticService__()::generateIndex(Index::INDEX_FILTERS);

        $normalizers = collect(
            [
                'lowercase' => [
                    'type' => 'custom',
                    'filter' => ['lowercase'],
                ],
            ]
        );

        $analysisMapping = new Index\AnalysisMapping($index, collect(), collect(), $normalizers);

        $mappings = collect();

        $this->_subjects()->each(
            static function (SetupableContract $setupable) use ($index, $mappings) {
                try {
                    $setupable->asMappings($index)->collection()->each(
                        static function (Mapping $mapping) use ($mappings) {
                            switch ($mapping->type()) {
                                case Mapping::TYPE_STRING:
                                    $mappings->put(
                                        $mapping->attribute(),
                                        [
                                            'type' => 'text',
                                            'fields' => [
                                                'keyword' => [
                                                    'type' => 'keyword',
                                                    'ignore_above' => ElasticServiceContract::KEYWORD_IGNORE_ABOVE_LENGTH,
                                                ],
                                                'sort' => [
                                                    'type' => 'keyword',
                                                    'normalizer' => 'lowercase',
                                                ],
                                            ],
                                        ]
                                    );
                                break;
                                case Mapping::TYPE_NUMBER:
                                    $mappings->put(
                                        $mapping->attribute(),
                                        [
                                            'type' => 'text',
                                            'fields' => [
                                                'keyword' => [
                                                    'type' => 'keyword',
                                                    'ignore_above' => ElasticServiceContract::KEYWORD_IGNORE_ABOVE_LENGTH,
                                                ],
                                                'numeric' => [
                                                    'type' => 'integer',
                                                ],
                                                'sort' => [
                                                    'type' => 'keyword',
                                                    'normalizer' => 'lowercase',
                                                ],
                                            ],
                                        ]
                                    );
                                break;
                                case Mapping::TYPE_DATE:
                                    $mappings->put(
                                        $mapping->attribute(),
                                        [
                                            'type' => 'date',
                                        ]
                                    );
                                break;
                            }
                        }
                    );
                    app()->forgetInstance(get_class($setupable));
                } catch (IndexNotSupported $exception) {
                }
            }
        );

        $this->elasticService__()->recreateIndex($analysisMapping, $mappings);
    }

    /**
     * @return Collection
     */
    private function _subjects(): Collection
    {
        return collect(self::SUBJECTS)->map(
            static function (string $class) {
                return app()->make($class);
            }
        );
    }
}
