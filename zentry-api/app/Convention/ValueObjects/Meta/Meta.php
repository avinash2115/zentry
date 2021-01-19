<?php

namespace App\Convention\ValueObjects\Meta;

use App\Convention\Contracts\Arrayable;

/**
 * Class Meta
 *
 * @package App\Convention\ValueObjects\Meta
 */
final class Meta implements Arrayable
{
    /**
     * @var null|Pagination
     */
    private ?Pagination $pagination = null;

    /**
     * @param Pagination $pagination
     *
     * @return Meta
     */
    public function addPagination(Pagination $pagination): Meta
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * @return Pagination|null
     */
    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'pagination' => $this->pagination() instanceof Pagination ? $this->pagination()->toArray() : [],
        ];
    }
}
