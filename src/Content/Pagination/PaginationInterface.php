<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Pagination;

interface PaginationInterface extends \Countable
{
    public function setPage(int $page);
    public function getPage(): int;
    public function getTotalResults(): int;
    public function getResultsPerPage(): int;
    public function isFirstPage(): bool;
    public function isLastPage(): bool;

    /**
     * Get current page range starting result
     *
     * @return int
     */
    public function getFrom(): int;

    /**
     * Get current page range ending result
     *
     * @return int
     */
    public function getTo(): int;

    /**
     * Return number of result pages
     *
     * @return int
     */
    public function getTotalPages(): int;
}
