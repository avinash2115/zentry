<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API;

use \Arr;
use \InvalidArgumentException;

/**
 * Class Meta
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API
 */
class Meta
{
    /**
     * @var int
     */
    private int $currentPage = 0;

    /**
     * @var int|null
     */
    private ?int $nextPage   = null;

    /**
     * @var int|null
     */
    private ?int $prevPage   = null;

    /**
     * @var int
     */
    private int $totalPages  = 0;

    /**
     * @var int
     */
    private int $totalCount  = 0;

    /**
     * Meta constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (Arr::has($args, 'current_page') && is_int(Arr::get($args, 'current_page'))) {
            $this->currentPage = Arr::get($args, 'current_page');
        } else {
            throw new InvalidArgumentException('"current_page" must be present and be integer');
        }

        if (Arr::has($args, 'next_page') && Arr::get($args, 'next_page') !== null && is_int(Arr::get($args, 'next_page'))) {
            $this->nextPage = Arr::get($args, 'next_page');
        }

        if (Arr::has($args, 'prev_page') && Arr::get($args, 'prev_page') !== null && is_int(Arr::get($args, 'prev_page'))) {
            $this->prevPage = Arr::get($args, 'prev_page');
        }

        if (Arr::has($args, 'total_pages') && is_int(Arr::get($args, 'total_pages'))) {
            $this->totalPages = Arr::get($args, 'total_pages');
        } else {
            throw new InvalidArgumentException('"total_pages" must be present and be integer');
        }

        if (Arr::has($args, 'total_count') && is_int(Arr::get($args, 'total_count'))) {
            $this->totalCount = Arr::get($args, 'total_count');
        } else {
            throw new InvalidArgumentException('"total_count" must be present and be integer');
        }
    }

    /**
     * @return int
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int|null
     */
    public function nextPage(): ?int
    {
        return $this->nextPage;
    }

    /**
     * @return int|null
     */
    public function prevPage(): ?int
    {
        return $this->prevPage;
    }

    /**
     * @return int
     */
    public function totalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function totalCount(): int
    {
        return $this->totalCount;
    }
}