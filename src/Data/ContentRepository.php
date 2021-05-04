<?php

declare(strict_types=1);

namespace Strata\Frontend\Data;

use Psr\Cache\CacheItemPoolInterface;
use Strata\Data\Cache\DataCache;
use Strata\Data\DataProviderInterface;
use Strata\Data\Exception\CacheException;
use Strata\Data\Helper\UnionTypes;
use Strata\Data\Traits\EventDispatcherTrait;
use Strata\Frontend\Content\BaseContent;
use Strata\Frontend\Content\Page;
use Strata\Frontend\Exception\RepositoryException;
use Strata\Frontend\Schema\ContentType;
use Strata\Frontend\Schema\Schema;
use Strata\Frontend\Schema\SchemaFactory;
use Strata\Frontend\Traits\CacheTrait;
use Strata\Frontend\Exception\ApiException;
use Strata\Frontend\Exception\ContentTypeNotSetException;

/**
 * Base class for CMS functionality
 *
 * Purpose is to read content from API and map to content objects
 *
 * @package Strata\Frontend\Data
 */
abstract class ContentRepository
{
    //use CacheTrait;
    use EventDispatcherTrait;

    protected DataProviderInterface $provider;
    protected ?Schema $contentSchema = null;
    protected ?ContentType $contentType = null;
    protected $cacheKey;

    /**
     * Set data provider
     *
     * @param DataProviderInterface $provider
     * @return ContentRepository Fluent interface
     */
    public function setProvider(DataProviderInterface $provider): ContentRepository
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Return data provider to use to retrieve data
     *
     * @return DataProviderInterface`
     */
    public function getProvider(): DataProviderInterface
    {
        return $this->provider;
    }

    /**
     * Decode data
     *
     * @return mixed
     */
    public function decode($data)
    {
        return $this->getProvider()->decode($data);
    }

    /**
     * Set the content model
     *
     * @param Schema|string $contentSchema
     * @return ContentRepository Fluent interface
     */
    public function setContentSchema($contentSchema): ContentRepository
    {
        UnionTypes::assert('$contentSchema', $contentSchema, 'string', 'Strata\Frontend\Schema\Schema');

        if ($contentSchema instanceof Schema) {
            $this->contentSchema = $contentSchema;
            return $this;
        }
        $this->contentSchema = SchemaFactory::createFromYaml($contentSchema);
        return $this;
    }

    /**
     * Return the content model
     *
     * @return Schema
     */
    public function getContentSchema(): Schema
    {
        if (!($this->contentSchema instanceof Schema)) {
            throw new RepositoryException('Content schema not set, you must set this via setContentSchema()');
        }
        return $this->contentSchema;
    }

    /**
     * Set the requested content type
     *
     * @param string $type
     * @return ContentRepository
     */
    public function setContentType(string $type): ContentRepository
    {
        if (!$this->contentTypeExists($type)) {
            throw new ContentTypeNotSetException(sprintf('Content type %s does not exist', $type));
        }
        $this->contentType = $this->getContentSchema()->getContentType($type);

        return $this;
    }

    /**
     * Return the current content type
     *
     * @return ContentType
     * @throws ContentTypeNotSetException
     */
    public function getContentType(): ContentType
    {
        if (!$this->hasContentType()) {
            throw new ContentTypeNotSetException('Content type is not set!');
        }
        return $this->contentType;
    }

    /**
     * Return the content type API endpoint
     *
     * Uses sprintf to parse parameters into the API endpoint
     *
     * @see https://www.php.net/manual/en/function.sprintf.php
     * @param ...$params Parameters to parse into the API endpoint
     * @return string
     * @throws ContentTypeNotSetException
     */
    public function getContentApiEndpoint(...$params): string
    {
        if (!$this->hasContentType()) {
            throw new ContentTypeNotSetException('Content type is not set!');
        }

        return sprintf($this->getContentType()->getApiEndpoint(), ...$params);
    }

    /**
     * Set and enable cache
     *
     * @param CacheItemPoolInterface $cacheItemPool
     * @return ContentRepository Fluent interface
     */
    public function setCache(CacheItemPoolInterface $cacheItemPool): ContentRepository
    {
        $this->getProvider()->setCache($cacheItemPool);
        return $this;
    }

    /**
     * Is the cache enabled?
     *
     * @return bool
     */
    public function isCacheEnabled(): bool
    {
        return $this->getProvider()->isCacheEnabled();
    }

    /**
     * Return the cache
     *
     * @return DataCache
     */
    public function getCache(): DataCache
    {
        return $this->getProvider()->getCache();
    }

    /**
     * Enable cache for subsequent data requests
     *
     * @param ?int $lifetime
     * @throws CacheException If cache not set
     */
    public function enableCache(?int $lifetime = null)
    {
        return $this->getProvider()->enableCache($lifetime);
    }

    /**
     * Disable cache for subsequent data requests
     *
     */
    public function disableCache()
    {
        return $this->getProvider()->disableCache();
    }

    /**
     * Set cache tags to apply to all future saved cache items
     *
     * To remove tags do not pass any arguments and tags will be reset to an empty array
     *
     * @param array $tags
     * @throws CacheException
     */
    public function setCacheTags(array $tags = [])
    {
        return $this->getProvider()->setCacheTags($tags);
    }

    // @todo old cache methods below

    /**
     * Set cache key for current content request
     *
     * @param string $key
     */
    public function setCacheKey(string $key)
    {
        $this->cacheKey = $this->filterCacheKey($key);
    }

    /**
     * Get cache key for current request
     *
     * If not set, build it from passed params
     *
     * @param mixed ...$params
     * @return string
     * @throws ApiException
     */
    public function getCacheKey(...$params)
    {
        if (empty($this->cacheKey)) {
            return $this->buildCacheKey($params);
        }
        return $this->cacheKey;
    }

    /**
     * Does the content type exist?
     *
     * @param string $type
     * @return bool
     */
    public function contentTypeExists(string $type): bool
    {
        return $this->getContentSchema()->hasContentType($type);
    }

    /**
     * Do we have a valid content type and content model set?
     *
     * @return bool
     */
    public function hasContentType(): bool
    {
        if ($this->contentSchema instanceof Schema && $this->contentType instanceof ContentType) {
            return true;
        }
        return false;
    }

    /**
     * Create a new page object
     *
     * This represents a content object (e.g. news article, page, case study, etc)
     *
     * @param array $data
     * @return Page
     */
    public function createPage(array $data): Page
    {
        $page = new Page();
        $page->setContentType($this->getContentType());
        $this->setContentFields($page, $data);

        return $page;
    }

    /**
     * Filter cache key to ensure it is safe to use
     *
     * @param $string
     * @return string
     */
    public function filterCacheKey($string): string
    {
        $string = (string) $string;
        $string = preg_replace('![{}()\@:]!', '', $string);
        $string = preg_replace('![\s/]!', '-', $string);
        return $string;
    }

    /**
     * Build a cache key
     *
     * @param mixed ...$params An array of strings or single-level arrays used to build a cache key
     * @return string
     * @throws ApiException
     */
    public function buildCacheKey(...$params): string
    {
        $elements = [];
        foreach ($params as $param) {
            switch (gettype($param)) {
                case 'string':
                    if (!empty($param)) {
                        $elements[] = $this->filterCacheKey($param);
                    }
                    break;
                case 'integer':
                case 'double':
                    $elements[] = $this->filterCacheKey($param);
                    break;
                case 'boolean':
                    $elements[] = ($param) ? 'true' : 'false';
                    break;
                case 'NULL':
                    $elements[] = 'NULL';
                    break;
                case 'array':
                    foreach ($param as $key => $value) {
                        if (is_array($value)) {
                            throw new ApiException('Cannot build cache key from a multidimensional array');
                        }
                        $elements[] = $this->filterCacheKey($key) . '=' . $this->filterCacheKey($value);
                    }
                    break;
                default:
                    throw new ApiException(sprintf('Cannot build cache key from passed param, type: %s', gettype($param)));
            }
        }
        if (empty($elements)) {
            $elements[] = 'cache';
        }
        return implode('.', $elements);
    }
}
