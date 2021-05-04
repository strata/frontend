<?php

declare(strict_types=1);

namespace Strata\Frontend\Data;

use Strata\Data\Mapper\MapperInterface;
use Strata\Frontend\Content\Field\ContentFieldInterface;
use Strata\Frontend\Data\Resolver\ResolverInterface;
use Strata\Frontend\Schema\ContentType;

interface RepositoryMapperInterface
{
    public function setContentType(ContentType $contentType);

    public function getContentType(): ContentType;

    public function setContentFieldResolver(ResolverInterface $contentFieldResolver);

    public function getContentFieldResolver(): ResolverInterface;

    public function setMapper(MapperInterface $mapper);

    public function getMapper(): MapperInterface;

    /**
     * Map source data to
     *
     * @param array $data
     * @param string|null $rootProperty
     * @return mixed
     */
    public function map(array $data, ?string $rootProperty = null);

    /**
     * Return array of custom content field objects from source data
     *
     * @param array $data Source data
     * @return ContentFieldInterface[] Array of ContentFieldInterface objects
     */
    public function mapCustomContentFields(array $data): array;

    /**
     * Return array of head meta fields from source data
     *
     * @param array $data Source data
     * @return array Array of meta key => values
     */
    public function mapHeadMeta(array $data): array;
}
