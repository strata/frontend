<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\Repository\CraftCms\Mapper;

use Strata\Data\Mapper\MapItem;
use Strata\Data\Transform\Data\CallableData;
use Strata\Data\Transform\Value\DateTimeValue;
use Strata\Data\Transform\Value\IntegerValue;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Data\MapItemTrait;
use Strata\Frontend\Data\RepositoryMapperInterface;

class MapPage implements RepositoryMapperInterface
{
    use MapItemTrait;

    public function __construct()
    {
        $mapper = new MapItem($this->getDefaultMapping());
        $mapper->toObject(Page::class);
        $this->setMapper($mapper);

        $this->setContentFieldResolver(new CraftCmsContentFieldResolver());
    }

    /**
     * Map source data to page object
     *
     * @param array $data
     * @param string|null $rootProperty
     * @return Page
     * @throws \Strata\Data\Exception\MapperException
     */
    public function map(array $data, ?string $rootProperty = null) //: Page
    {
        /** @var Page $page */
        $page = $this->getMapper()->map($data, $rootProperty);
        $page->setContentType($this->getContentType());
        return $page;
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
            'title'         => '[title]',
            'urlSlug'       => '[slug]',
            'datePublished' => new DateTimeValue('[postDate]'),
            'status'        => '[status]',
            'content'       => new CallableData([$this, 'mapCustomContentFields']),
            'head.title'    => '[title]',
            'head.meta'     => new CallableData([$this, 'mapHeadMeta']),

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

}