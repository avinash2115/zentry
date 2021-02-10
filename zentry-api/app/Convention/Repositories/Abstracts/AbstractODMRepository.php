<?php

namespace App\Convention\Repositories\Abstracts;

use App\Assistants\Transformers\ValueObjects\JsonApiResponseBuilder;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository as BaseDocumentRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class AbstractODMRepository
 *
 * @package App\Convention\Repositories\Abstracts
 */
abstract class AbstractODMRepository implements RepositoryContract
{
    /**
     * @var DocumentManager
     */
    private ?DocumentManager $manager = null;

    /**
     * @var BaseDocumentRepository
     */
    private ?BaseDocumentRepository $documentRepository = null;

    /**
     * @var Builder
     */
    private Builder $builder;

    /**
     * @var string
     */
    private string $className;

    /**
     * @var string
     */
    private string $alias;

    /**
     * @param string $className
     * @param string $alias
     *
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    public function __construct(string $className, string $alias)
    {
        $this->className = $className;
        $this->alias = $alias;

        $this->refreshBuilder();
    }

    /**
     * @return DocumentManager
     * @throws BindingResolutionException
     */
    public function manager(): DocumentManager
    {
        if (!$this->manager instanceof DocumentManager) {
            $this->manager = app()->make(DocumentManager::class);
        }

        return $this->manager;
    }

    /**
     * @return DocumentRepository
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    public function documentRepository(): BaseDocumentRepository
    {
        if (!$this->documentRepository instanceof BaseDocumentRepository) {
            /**
             * @var BaseDocumentRepository $documentRepository
             */
            $documentRepository = $this->manager()->getRepository($this->getClassName());

            $this->documentRepository = $documentRepository;
        }

        return $this->documentRepository;
    }

    /**
     * @return Builder
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    protected function builder(): Builder
    {
        if (!$this->builder instanceof Builder) {
            $this->setBuilder();
        }

        return $this->builder;
    }

    /**
     * @return Builder
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    private function setBuilder(): Builder
    {
        $this->builder = $this->documentRepository()->createQueryBuilder();

        return $this->builder;
    }

    /**
     * @return Builder
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    protected function refreshBuilder(): Builder
    {
        return $this->setBuilder();
    }

    /**
     * @return string
     * @throws UnexpectedValueException
     */
    protected function getClassName(): string
    {
        if (strEmpty($this->className)) {
            throw new UnexpectedValueException('Class name cannot be empty');
        }

        return $this->className;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getAlias(): string
    {
        if (strEmpty($this->alias)) {
            throw new InvalidArgumentException('Alias cannot be empty');
        }

        return $this->alias;
    }

    /**
     * @param callable|null $callback
     *
     * @return Collection
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws MongoDBException
     * @throws UnexpectedValueException
     */
    public function getAll(?callable $callback = null): Collection
    {
        $sortBy = app()->make(JsonApiResponseBuilder::class)->sortBy();

        if ($sortBy->isNotEmpty()) {
            $this->sortBy($sortBy->get($this->getAlias(), []));
            $this->builder()->sort('id', 'ASC');
        }

        $results = $this->builder()->getQuery()->execute();

        $this->refreshBuilder();

        return collect($results);
    }

    /**
     * @inheritdoc
     */
    public function sortBy(array $data, string $alias = null): RepositoryContract
    {
        foreach ($data as $relation => $column) {
            $subject = (string)$column;

            $route = strpos($subject, '-') === false ? 'ASC' : 'DESC';

            $subject = str_replace('-', '', $subject);
            $subject = Str::camel($subject);

            $this->builder()->sort(($subject), $route);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMaxResults(int $numberOfResults): RepositoryContract
    {
        $this->builder()->limit($numberOfResults);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): RepositoryContract
    {
        $this->builder()->skip($offset);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function join(
        string $relation,
        string $alias = null,
        string $type = self::INNER_JOIN_TYPE,
        bool $addSelect = false,
        string $conditionType = null,
        string $condition = null
    ): string {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }
}
