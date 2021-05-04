<?php

declare(strict_types=1);

namespace Strata\Frontend\Data\Repository\WordPress\Mapper;

use Strata\Frontend\Data\Resolver\ContentFieldResolver;

class WordpressContentFieldResolver extends ContentFieldResolver
{
    /**
     * Fieldname to identify flexible component fields
     * @var string
     */
    protected string $flexibleComponentNameField = 'acf_fc_layout';
}
