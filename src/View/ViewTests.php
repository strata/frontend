<?php

declare(strict_types=1);

namespace Strata\Frontend\View;

/**
 * View template tests for boolean decisions
 *
 * Methods should return boolean
 */
class ViewTests
{
    /**
     * Are we on production?
     *
     * @param string $environment Current environment
     * @param string $prod Production environment, defaults to 'prod'
     * @return bool
     */
    public static function isProd($environment, $prod = 'prod'): bool
    {
        return ($environment === $prod);
    }
}
