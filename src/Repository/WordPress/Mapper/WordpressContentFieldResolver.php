<?php

declare(strict_types=1);

namespace Strata\Frontend\Repository\WordPress\Mapper;

use Strata\Frontend\Repository\Resolver\ContentFieldResolver;

class WordpressContentFieldResolver extends ContentFieldResolver
{
    /**
     * Fieldname to identify flexible component fields
     * @var string
     */
    protected string $flexibleComponentNameField = 'acf_fc_layout';
}
