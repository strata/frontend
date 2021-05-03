<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\WordPress\Mapper;

use Strata\Data\Exception\MapperException;
use Strata\Data\Mapper\MapItem;
use Strata\Data\Mapper\MapperInterface;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Strata\Data\Transform\Value\IntegerValue;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\MapItemTrait;
use Strata\Frontend\Data\RepositoryMapperInterface;
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
            'content'       => ['[content][rendered]', '[post_content]'],
//            'content'       => new CallableData([$this, 'populateContent']),

            //'dateModified'  => new DateTimeValue(['[modified]', '[post_modified]']),
            //'excerpt'       => ['[excerpt][rendered]', '[post_excerpt]'],
            //'template'      => ['[template]', '[page_template]'],
            //'featuredImage' => '[featured_media]',    // @todo requires API call
            //'content'       => ['[content][rendered]', '[post_content]'], // @todo review addField method https://symfony.com/doc/current/components/property_access.html#writing-to-array-properties
        ];
    }

    public function mapHead(array $data)
    {
        return [];
    }

    public function getContentField(array $sourceData, string $destinationPath, Page $item): array
    {
        foreach ($sourceData as $name => $value) {
            
        }
        return [];
    }

    public function map(array $data, ?string $rootProperty = null): Page
    {
        $page = $this->mapper->map($data, $rootProperty);
        //$page = $this->mapHead($page, $data);
        //$page = $this->mapContentFields($page);

        return $page;
    }

}