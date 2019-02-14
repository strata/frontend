<?php

namespace Studio24\Frontend\Content\Field;

use Studio24\Frontend\Collection\ArrayAccessTrait;

class ContentFieldCollection extends \ArrayIterator
{
    /**
     * Add an item to the collection
     *
     * @param ContentFieldInterface $item
     * @return ContentFieldCollection Fluent interface
     */
    public function addItem(ContentFieldInterface $item) : ContentFieldCollection
    {
        $this->offsetSet($item->getName(), $item);
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
     * Return string representation of content fields
     *
     * @return string
     */
    public function __toString(): string
    {
        $content = [];
        foreach ($this->collection as $item) {
            $content[] = $item->__toString();
        }
        return implode(' ', $content);
    }
}
