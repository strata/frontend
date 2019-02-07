<?php

namespace Studio24\Frontend\Traits;

use Psr\SimpleCache\CacheInterface;

trait CacheTrait
{
    /**
     * Cache object
     *
     * @var CacheInterface
     */
    public $cache;

    /**
     * Set the cache object
     *
     * @param CacheInterface $cache
     * @return CacheTrait Fluent interface
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Return cache object
     *
     * @return CacheInterface
     */
    public function getCache() : CacheInterface
    {
        return $this->cache;
    }

    /**
     * Do we have a usable cache?
     *
     * @return bool
     */
    public function hasCache()
    {
        return ($this->cache instanceof CacheInterface);
    }
}
