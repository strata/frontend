<?php

declare(strict_types=1);

namespace Strata\Frontend\Repository\CraftCms\Mapper;

use Strata\Frontend\Repository\Resolver\ContentFieldResolver;

class CraftCmsContentFieldResolver extends ContentFieldResolver
{
    /**
     * Fieldname to identify flexible component fields
     * @var string
     */
    protected string $flexibleComponentNameField = 'typeHandle';

}
