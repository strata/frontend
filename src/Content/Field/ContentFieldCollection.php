<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Collection\ArrayAccessTrait;

class ContentFieldCollection extends \ArrayIterator
{
    /**
     * Add an item to the collection
     *
     * @param ContentFieldInterface $item
     * @return ContentFieldCollection Fluent interface
     */
    public function addItem(ContentFieldInterface $item): ContentFieldCollection
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Remove an item from the collection
     * @param ContentFieldInterface $item
     * @return $this
     */
    public function removeItem(ContentFieldInterface $item)
    {
        if ($this->offsetExists($item->getName())) {
            $this->offsetUnset($item->getName());
        }
        return $this;
    }

    /**
     * Return current item
     *
     * @return ContentFieldInterface
     */
    public function current(): ContentFieldInterface
    {
        return parent::current();
    }

    /**
     * Get content by name
     *
     * @param $name
     * @return ContentFieldInterface or null on failure
     */
    public function get($name): ?ContentFieldInterface
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        } else {
            return null;
        }
    }

    /**
     * Return item by key
     *
     * @param string $index
     * @return ContentFieldInterface
     */
    public function offsetGet($index): ContentFieldInterface
    {
        return parent::offsetGet($index);
    }

    /**
     * Enable direct access to content fields
     *
     * @param $name
     * @param $arguments
     * @return ContentFieldInterface|null
     */
    public function __call($name, $arguments)
    {
        return $this->get($name);
    }

    /**
     * Return string representation of content fields
     *
     * @return string
     */
    public function __toString(): string
    {
        $content = [];
        foreach ($this as $item) {
            $content[] = $item->__toString();
        }
        return implode(' ', $content);
    }
}
