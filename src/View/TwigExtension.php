<?php

declare(strict_types=1);

namespace Strata\Frontend\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig custom functions and filters
 *
 * To use add this to your services.yaml:

# Register Frontend Twig helpers
Strata\Frontend\Twig\ViewHelpers:
tags: ['twig.extension']

 * @package Strata\Frontend\Twig
 */
class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction('slugify', ['ViewHelpers', 'slugify']),
            new TwigFunction('fix_url', ['ViewHelpers', 'fixUrl']),
            new TwigFunction('not_empty', ['ViewHelpers', 'notEmpty'], ['is_variadic' => true]),
            new TwigFunction('all_not_empty', ['ViewHelpers', 'allNotEmpty'], ['is_variadic' => true]),
            new TwigFunction('is_prod', ['ViewHelpers', 'isProd']),
            new TwigFunction('staging_banner', ['ViewHelpers', 'stagingBanner'], ['is_safe' => ['html']]),
        );
    }

    public function getFilters()
    {
        return [
            new TwigFunction('excerpt', ['ViewHelpers', 'excerpt']),
            new TwigFilter('build_version', ['ViewHelpers', 'buildVersion']),
        ];
    }
}
