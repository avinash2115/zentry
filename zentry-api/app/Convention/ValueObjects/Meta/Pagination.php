<?php

namespace App\Convention\ValueObjects\Meta;

use App\Assistants\Elastic\ValueObjects\Paginator;
use App\Convention\Contracts\Arrayable;

/**
 * Class Pagination
 *
 * @package App\Convention\ValueObjects\Meta
 */
final class Pagination implements Arrayable
{
    /**
     * @var Paginator
     */
    private Paginator $paginator;

    /**
     * @var int
     */
    private int $total;

    /**
     * @param Paginator $paginator
     * @param int       $total
     */
    public function __construct(Paginator $paginator, int $total)
    {
        $this->paginator = $paginator;
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function totalRecords(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function totalPages(): int
    {
        if ($this->totalRecords() === 0 || !$this->paginator->enabled()) {
            return 0;
        }

        return $this->lastPage($this->totalRecords(), $this->paginator->limit());
    }

    /**
     * @return int
     */
    public function page(): int
    {
        if ($this->paginator->offset() < $this->totalRecords()) {
            return $this->paginator->page();
        }

        return $this->lastPage($this->total, $this->paginator->limit());
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        return $this->paginator->limit();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'page' => $this->page(),
            'total_records' => $this->totalRecords(),
            'total_pages' => $this->totalPages(),
            'limit' => $this->limit(),
        ];
    }

    /**
     * @param int $total
     * @param int $limit
     *
     * @return int
     */
    private function lastPage(int $total, int $limit): int
    {
        $mod = $total % $limit;

        $fullPages = intdiv($total, $limit);

        return $mod === 0 ? $fullPages : $fullPages + 1;
    }
}
