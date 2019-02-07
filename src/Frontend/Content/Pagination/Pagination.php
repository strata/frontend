<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Pagination;

use Studio24\Frontend\Exception\PaginationException;

/**
 * Simple class to manage API pagination
 *
 * @package Studio24\Frontend\Api
 */
class Pagination implements PaginationInterface
{
    protected $page = 1;
    protected $totalPages = 0;
    protected $totalResults = 0;
    protected $resultsPerPage = 20;

    /**
     * Implement count(), returns total number of pages
     *
     * @return int
     */
    public function count()
    {
        return $this->getTotalPages();
    }

    /**
     * Set the current page number
     *
     * @param int $page
     * @return Pagination
     * @throws PaginationException
     */
    public function setPage(int $page): Pagination
    {
        if ($page > $this->getTotalPages()) {
            throw new PaginationException(sprintf(
                'Invalid page %s, only %s pages available. If you are using custom results per page
                        make sure you call Pagination::setResultsPerPage() before Pagination::setPage()',
                $page,
                $this->getTotalPages()
            ));
        }
        $this->page = $page;
        return $this;
    }

    /**
     * Return current page number
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Set total results in pagination
     *
     * @param int $total
     * @return Pagination
     */
    public function setTotalResults(int $total): Pagination
    {
        $this->totalResults = $total;
        return $this;
    }

    /**
     * Return total number of results
     *
     * @return int
     */
    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * Set number of results per page, default if not set: 20
     *
     * @param int $number
     * @return Pagination
     */
    public function setResultsPerPage(int $number): Pagination
    {
        $this->resultsPerPage = $number;

        // Reset total pages since this will need to be re-calculated
        $this->totalPages = 0;

        return $this;
    }

    /**
     * Return number of results per page
     *
     * @return int
     */
    public function getResultsPerPage(): int
    {
        return $this->resultsPerPage;
    }

    /**
     * Set the total number of pages in the pagination result set
     *
     * @param int $total
     * @return Pagination
     */
    public function setTotalPages(int $total): Pagination
    {
        $this->totalPages = $total;
        return $this;
    }

    /**
     * Return total number of pages in pagination
     *
     * @return int
     */
    public function getTotalPages(): int
    {
        // Auto-generate
        if (empty($this->totalPages) && !empty($this->getTotalResults()) && !empty($this->getResultsPerPage())) {
            $this->totalPages = (int) ceil($this->getTotalResults() / $this->getResultsPerPage());
        }

        return $this->totalPages;
    }

    /**
     * Get current page range starting result
     *
     * @return int
     */
    public function getFrom(): int
    {
        if ($this->isFirstPage()) {
            return 1;
        }
        return ((($this->getPage() - 1) * $this->getResultsPerPage()) + 1);
    }

    /**
     * Get current page range ending result
     *
     * @return int
     */
    public function getTo(): int
    {
        if ($this->isLastPage()) {
            return $this->getTotalResults();
        }
        return (($this->getPage() * $this->getResultsPerPage()));
    }

    /**
     * Is this the first page?
     *
     * @return bool
     */
    public function isFirstPage(): bool
    {
        return ($this->getPage() === 1);
    }

    /**
     * Is this the last page?
     *
     * @return bool
     */
    public function isLastPage(): bool
    {
        return ($this->getPage() === $this->getTotalPages());
    }

    /**
     * Return first page, or current page if on first page
     *
     * @return int
     */
    public function getPrevious(): int
    {
        if (!$this->isFirstPage()) {
            return $this->getPage() - 1;
        }
        return $this->getPage();
    }

    /**
     * Return next page, or current page if on last page
     *
     * @return int
     */
    public function getNext():int
    {
        if (!$this->isLastPage()) {
            return $this->getPage() + 1;
        }
        return $this->getPage();
    }

    /**
     * Return first page
     *
     * @return int
     */
    public function getFirst(): int
    {
        return 1;
    }

    /**
     * Return last page
     *
     * @return int
     */
    public function getLast(): int
    {
        return $this->getTotalPages();
    }

    /**
     * Return array of page numbers to include in pagination links
     *
     * @param int $maxPages
     * @return array
     */
    public function getPageLinks($maxPages = 5): array
    {
        $from = 1;
        $to = $maxPages;

        if ($this->getTotalPages() <= $maxPages) {
            return range($from, $to);
        }

        $currentPage = $this->getPage();
        $half = (int) ceil($maxPages / 2);

        if ($currentPage <= $half) {
            return range($from, $to);
        }

        $from = $currentPage - $half + 1;
        $to = $currentPage + ($maxPages - $half);

        if ($to > $this->getTotalPages()) {
            $to = $this->getTotalPages();
            $from = $to - $maxPages + 1;
        }

        return range($from, $to);
    }
}
