<?php

declare(strict_types=1);

namespace Strata\Frontend\Data;

use Strata\Data\Mapper\MapCollection;
use Strata\Data\Mapper\MapperInterface;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Exception\MapperException;

/**
 * Common functionality for collection mappers
 */
trait MapCollectionTrait
{
    protected ?array $paginationData = null;
    protected $resultsPerPage = null;
    protected ?int $maxPerPage = null;
    protected $currentPage = null;
    protected $totalResults = null;

    /**
     * @return array|null
     */
    public function getPaginationData(): ?array
    {
        return $this->paginationData;
    }

    /**
     * @param array $paginationData
     */
    public function setPaginationData(array $paginationData): void
    {
        $this->paginationData = $paginationData;
    }

    /**
     * @return null
     */
    public function getTotalResults()
    {
        return $this->totalResults;
    }

    /**
     * @param null $totalResults
     */
    public function setTotalResults($totalResults): void
    {
        $this->totalResults = $totalResults;
    }

    /**
     * @return int|string|null
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int|string $resultsPerPage
     * @throws MapperException
     */
    public function setResultsPerPage($resultsPerPage): void
    {
        if (null !== $this->maxPerPage && $resultsPerPage > $this->maxPerPage) {
            throw new MapperException(sprintf('You cannot set $resultsPerPage to %s since maximum per page is %s', $resultsPerPage, $this->maxPerPage));
        }
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @return int|string|null
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int|string $currentPage
     */
    public function setCurrentPage($currentPage): void
    {
        $this->currentPage = $currentPage;
    }

}