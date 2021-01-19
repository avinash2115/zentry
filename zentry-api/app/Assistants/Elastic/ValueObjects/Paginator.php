<?php

namespace App\Assistants\Elastic\ValueObjects;

/**
 * Class Paginator
 */
final class Paginator
{
    public const PARAMETER = 'pagination';

    /**
     * @var int
     */
    private int $page;

    /**
     * @var int
     */
    private int $limit;

    /**
     * @var int
     */
    private int $offset;

    /**
     * @var bool
     */
    private bool $enabled = false;

    /**
     * @param int $page
     * @param int $limit
     */
    public function __construct(int $page = 0, int $limit = -1)
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->init($page, $limit);
        $this->setOffset();
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return Paginator
     */
    private function init(int $page, int $limit): Paginator
    {
        if ($page < 1 || $limit < 1) {
            $this->disable();
        } else {
            $this->enable();
        }

        return $this;
    }

    /**
     * @return Paginator
     */
    private function disable(): Paginator
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * @return Paginator
     */
    private function enable(): Paginator
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function page(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function offset(): int
    {
        return $this->offset;
    }

    /**
     * @return $this
     */
    private function setOffset(): Paginator
    {
        if (!$this->enabled() || $this->limit() < 1 || $this->page() < 1) {
            $this->offset = 0;
        } else {
            $this->offset = ($this->page() - 1) * $this->limit();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function enabled(): bool
    {
        return $this->enabled;
    }
}
