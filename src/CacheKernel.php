<?php

declare(strict_types=1);

namespace Strata\Frontend;

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

/**
 * HTTP CacheKernel
 *
 * @see https://symfony.com/doc/current/http_cache.html
 * @package Strata\Frontend
 */
class CacheKernel extends HttpCache
{

    /**
     * Set default options
     *
     * @see https://symfony.com/doc/current/http_cache.html
     * @return array
     */
    protected function getOptions()
    {
        return [
            'default_ttl' => 3600,
        ];
    }
}
