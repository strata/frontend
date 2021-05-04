<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\Repository\CraftCms\Mapper;

use Strata\Frontend\Data\Resolver\ContentFieldResolver;

class CraftCmsContentFieldResolver extends ContentFieldResolver
{
    /**
     * Fieldname to identify flexible component fields
     * @var string
     */
    protected string $flexibleComponentNameField = 'typeHandle';

}
