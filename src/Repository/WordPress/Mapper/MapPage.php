<?php

declare(strict_types=1);

namespace Strata\Frontend\Repository\WordPress\Mapper;

use Strata\Data\Exception\MapperException;
use Strata\Data\Mapper\MapItem;
use Strata\Data\Mapper\MapperInterface;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Strata\Data\Transform\Value\IntegerValue;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Repository\MapItemTrait;
use Strata\Frontend\Repository\RepositoryMapperInterface;
use Strata\Frontend\Schema\Schema;

class MapPage implements RepositoryMapperInterface
{
    use MapItemTrait;

    public function __construct()
    {
        $this->mapper = new MapItem($this->getDefaultMapping());
        $this->mapper->toObject(Page::class);
    }

    /**
     * Return default mapping to use with mapper
     *
     * @return array
     */
    public function getDefaultMapping(): array
    {
        return [
            'id'            => new IntegerValue('[id]'),
            'title'         => ['[title][rendered]', '[post_title]', '[post_name]'],
            'datePublished' => new DateTimeValue(['[date]', '[post_date]']),
            'dateModified'  => new DateTimeValue(['[modified]', '[post_modified]']),
            'status'        => ['[status]', '[post_status]'],
            'urlSlug'       => ['[slug]', '[post_name]'],
            'excerpt'       => ['[excerpt][rendered]', '[post_excerpt]'],
            'template'      => ['[template]', '[page_template]'],
            'content'       => new CallableData([$this, 'mapCustomContentFields']),
            'head.title'    => '[title]',
            'head.meta'     => new CallableData([$this, 'mapHeadMeta']),

//            'content'       => new CallableData([$this, 'populateContent']),

            //'dateModified'  => new DateTimeValue(['[modified]', '[post_modified]']),
            //'excerpt'       => ['[excerpt][rendered]', '[post_excerpt]'],
            //'template'      => ['[template]', '[page_template]'],
            //'featuredImage' => '[featured_media]',    // @todo requires API call
            //'content'       => ['[content][rendered]', '[post_content]'], // @todo review addField method https://symfony.com/doc/current/components/property_access.html#writing-to-array-properties
        ];
    }

    /**
     * Return array of head meta fields from source data
     *
     * @param array $data Source data
     * @return array Array of meta key => values
     */
    public function mapHeadMeta(array $data): array
    {
        $headFields = [];

        $headFields['og:title'] = $data['title'];

        return $headFields;
    }

    public function map(array $data, ?string $rootProperty = null): Page
    {
        $page = $this->mapper->map($data, $rootProperty);
        //$page = $this->mapHead($page, $data);
        //$page = $this->mapContentFields($page);

        return $page;
    }
}
