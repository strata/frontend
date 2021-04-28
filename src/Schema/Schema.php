<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema;

use Strata\Frontend\Collection\ArrayAccessTrait;

/**
 * Represents a content model schema
 *
 * This contains a collection of content types
 *
 * @package Strata\Frontend\BaseField
 */
class Schema extends \ArrayIterator
{
    private array $global = [];
    private array $contentTypes = [];

    /**
     * Return a global property
     *
     * @param string $name Property name
     * @return mixed|null Property value, or null if not set
     */
    public function getGlobal(string $name)
    {
        if ($this->hasGlobal($name)) {
            return $this->global[$name];
        }
        return null;
    }

    /**
     * Return all schema global properties
     *
     * @return array
     */
    public function getGlobals(): array
    {
        return $this->global;
    }

    /**
     * Add a global property
     *
     * @param string $name
     * @param mixed $value
     */
    public function addGlobal(string $name, $value)
    {
        $this->global[$name] = $value;
    }

    /**
     * Whether a global property exists?
     *
     * @param string $name
     * @return bool
     */
    public function hasGlobal(string $name): bool
    {
        return isset($this->global[$name]);
    }

    /**
     * Whether the content type exists in the content model
     *
     * @param string $contentType
     * @return bool
     */
    public function hasContentType(string $contentType): bool
    {
        return $this->offsetExists($contentType);
    }

    /**
     * Return content type, if it exists
     *
     * @param string $contentType
     * @return ContentType|null
     */
    public function getContentType(string $contentType): ?ContentType
    {
        if ($this->hasContentType($contentType)) {
            return $this->offsetGet($contentType);
        }
        return null;
    }

    /**
     * Return a content type matched by the source content type
     *
     * E.g. For a content model that has News articles which are stored in WordPress as "posts",
     * BaseField::getBySourceContentType('posts') will return the News content type
     *
     * @param string $sourceContentType
     * @return ContentType|null
     */
    public function getBySourceContentType(string $sourceContentType): ?ContentType
    {
        foreach ($this as $contentType) {
            if ($contentType->getSourceContentType() === $sourceContentType) {
                return $contentType;
            }
        }
        return null;
    }

    /**
     * Add an item to the collection
     *
     * @param ContentType $item
     * @return Schema Fluent interface
     */
    public function addItem(ContentType $item): Schema
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Return current item
     *
     * @return ContentType
     */
    public function current(): ContentType
    {
        return parent::current();
    }

    /**
     * Return item by key
     *
     * @param string $key
     * @return ContentType
     */
    public function offsetGet($key): ContentType
    {
        return parent::offsetGet($key);
    }
}
