<?php

declare(strict_types=1);

namespace Strata\Frontend\Repository\CraftCms\Mapper;

use Strata\Data\Collection;
use Strata\Data\Mapper\MapCollection;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Repository\MapCollectionTrait;

class MapPageCollection extends MapPage
{
    use MapCollectionTrait;

    /**
     * Consructor
     * @param int|string $totalResults
     * @param int|string $resultsPerPage
     * @param int|string $currentPage
     * @param array|null $paginationData
     */
    public function __construct($totalResults = null, $resultsPerPage = null, $currentPage = 1, ?array $paginationData = null)
    {
        $mapper = new MapCollection($this->getDefaultMapping());
        $mapper->toObject(Page::class)
               ->setCollectionClass('Strata\Frontend\Content\PageCollection');
        $this->setMapper($mapper);

        $this->setContentFieldResolver(new CraftCmsContentFieldResolver());

        if (null !== $totalResults) {
            $this->setTotalResults($totalResults);
        }
        if (null !== $resultsPerPage) {
            $this->setResultsPerPage($resultsPerPage);
        }
        $this->setCurrentPage($currentPage);
        if (null != $paginationData) {
            $this->setPaginationData($paginationData);
        }
    }

    /**
     * Map source data to page object
     *
     * @param array $data
     * @param string|null $rootProperty
     * @return Page
     * @throws \Strata\Data\Exception\MapperException
     */
    public function map(array $data, ?string $rootProperty = null): Collection
    {
        /** @var Page $page */
        $collection = $this->getMapper()->map($data, $rootProperty);
        return $collection;
    }
}
