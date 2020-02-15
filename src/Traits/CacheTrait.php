<?php

declare(strict_types=1);

namespace Strata\Frontend\Traits;

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
     * Default cache lifetime (in seconds)
     *
     * @var int
     */
    public $defaultLifetime = 3600;

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
    public function getCache(): CacheInterface
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

    /**
     * Set default cache lifetime
     *
     * @param int $secs
     */
    public function setCacheLifetime(int $secs)
    {
        $this->defaultLifetime = $secs;
    }

    /**
     * Return default cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime(): int
    {
        return $this->defaultLifetime;
    }

    /**
     * Remove cache item
     *
     * @param string $key
     * @return bool True on success, false on error
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteCacheItem(string $key): bool
    {
        return $this->cache->delete($key);
    }
}
