<?php

declare(strict_types=1);

namespace Strata\Frontend\Data;

use Psr\Cache\CacheItemPoolInterface;
use Strata\Data\Cache\DataCache;
use Strata\Data\DataProviderInterface;
use Strata\Data\Exception\CacheException;
use Strata\Frontend\Schema\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * A repository is the entry point to access data from external sources
 */
interface RepositoryInterface
{
    /**
     * Set data provider
     *
     * @param DataProviderInterface $provider
     * @return mixed
     */
    public function setProvider(DataProviderInterface $provider);

    /**
     * Return data provider to use to retrieve data
     *
     * @return DataProviderInterface`
     */
    public function getProvider();

    /**
     * Decode data
     *
     * @return mixed
     */
    public function decode($data);

    /**
     * Set and enable cache
     *
     * @param CacheItemPoolInterface $cacheItemPool
     * @return mixed
     */
    public function setCache(CacheItemPoolInterface $cacheItemPool);

    /**
     * Is the cache enabled?
     *
     * @return bool
     */
    public function isCacheEnabled(): bool;

    /**
     * Return the cache
     *
     * @return DataCache
     */
    public function getCache(): DataCache;

    /**
     * Enable cache for subsequent data requests
     *
     * @param ?int $lifetime
     * @throws CacheException If cache not set
     */
    public function enableCache(?int $lifetime = null);

    /**
     * Disable cache for subsequent data requests
     *
     */
    public function disableCache();

    /**
     * Set cache tags to apply to all future saved cache items
     *
     * To remove tags do not pass any arguments and tags will be reset to an empty array
     *
     * @param array $tags
     * @throws CacheException
     */
    public function setCacheTags(array $tags = []);

    /**
     * Set content schema (from config file or schema object)
     *
     * @param $schema
     */
    public function setContentSchema($schema);

    /**
     * Return content schema to use with data repository
     *
     * @return Schema
     */
    public function getContentSchema(): Schema;

    /**
     * Adds an event listener that listens on the specified event
     *
     * @param string $eventName Event name
     * @param callable $listener The listener
     * @param int      $priority The higher this value, the earlier an event
     *                           listener will be triggered in the chain (defaults to 0)
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0);

    /**
     * Adds an event subscriber
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);

    /**
     * Dispatches an event to all registered listeners
     *
     * @param Event $event The event to pass to the event handlers/listeners
     * @param string $eventName The name of the event to dispatch
     * @return Event The passed $event MUST be returned
     */
    public function dispatchEvent(Event $event, string $eventName): Event;
}
