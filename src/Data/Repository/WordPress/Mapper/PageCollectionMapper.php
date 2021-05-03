<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\Repository\WordPress;

use Strata\Data\Mapper\MapCollection;
use Strata\Data\Mapper\MapItem;
use Strata\Data\Mapper\MapperAbstract;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\BaseValue;
use Strata\Data\Transform\Value\DateTimeValue;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\MapCollectionTrait;
use Strata\Frontend\Data\MapItemTrait;
use Strata\Frontend\Data\RepositoryMapperInterface;

class PageRepositoryMapTrait extends MapCollectionTrait implements RepositoryMapperInterface
{
    /**
     * Max number of results per page limited by API
     * @var int|null
     */
    protected ?int $maxPerPage = 100;

    public function getDefaultMapping(): array
    {
        // @todo
        return [
            'id'            => new IntegerValue('[id]'),
            'title'         => ['[title][rendered]', '[post_title]', '[post_name]'],
            'datePublished' => new DateTimeValue(['[date]', '[post_date]']),
            'dateModified'  => new DateTimeValue(['[modified]', '[post_modified]']),
            'status'        => ['[status]', '[post_status]'],
            'urlSlug'       => ['[slug]', '[post_name]'],
            'excerpt'       => ['[excerpt][rendered]', '[post_excerpt]'],
            'template'      => ['[template]', '[page_template]'],
            'content'       => new CallableData([$this, 'populateContent'])

            //'dateModified'  => new DateTimeValue(['[modified]', '[post_modified]']),
            //'excerpt'       => ['[excerpt][rendered]', '[post_excerpt]'],
            //'template'      => ['[template]', '[page_template]'],
            //'featuredImage' => '[featured_media]',    // @todo requires API call
            //'content'       => ['[content][rendered]', '[post_content]'], // @todo review addField method https://symfony.com/doc/current/components/property_access.html#writing-to-array-properties
        ];
    }

    /**
     * @param array $paginationData Array of response headers
     * @param int $resultsPerPage
     * @param int $currentPage
     * @return MapCollection
     * @throws \Strata\Data\Exception\MapperException
     */
    public function getMapper(): MapCollection
    {
        $mapper = new MapCollection($this->getMapping());
        $mapper->toObject(Page::class)
               ->fromPaginationData($this->getPaginationData())
               ->totalResults('[x-wp-total][0]')
               ->resultsPerPage($this->getResultsPerPage())
               ->currentPage($this->getCurrentPage());

        return $mapper;
    }

}
