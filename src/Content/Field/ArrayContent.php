<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Exception\ContentFieldException;

/**
 * Array content field
 *
 * An array is a set of fixed content fields, with multiple entries
 *
 * E.g. it may contain two content fields for 'name' and 'image'
 * So an entry with two records would look like:
 *
 * $this->collection = [
 *     0 => ['name field', 'image field'],
 *     1 => ['name field', 'image field'],
 * ]
 *
 * @package Strata\Frontend\Content\Field
 */
class ArrayContent extends ContentField implements \SeekableIterator, \Countable
{
    protected $collection = [];
    protected $position = 0;

    /**
     * Create array collection content field
     *
     * @param string $name
     * @param array $value Array of child content fields
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, array $value = [])
    {
        $this->setName($name);

        if ($value !== null) {
            $this->setItems($value);
        }
    }

    /**
     * Return array of content fields
     *
     * @return array
     */
    public function getValue(): array
    {
        return $this->collection;
    }

    /**
     * Return string representation of content field
     *
     * @return string
     */
    public function __toString(): string
    {
        $content = '';
        foreach ($this->collection as $contentFields) {
            foreach ($contentFields as $field) {
                $content .= $field->__toString();
            }
        }

        return $content;
    }

    /**
     * Set multiple items at once
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
        return $this;
    }

    /**
     * Add an item to the collection
     *
     * @param ContentFieldCollection $item Collection of content fields
     * @return ArrayContent Fluent interface
     */
    public function addItem(ContentFieldCollection $item): ArrayContent
    {
        $this->collection[] = $item;
        return $this;
    }

    /**
     * @return ContentFieldCollection
     */
    public function current(): ContentFieldCollection
    {
        return $this->collection[$this->position];
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->collection[$this->position]);
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function count(): int
    {
        return count($this->collection);
    }

    public function seek($position)
    {
        if (!isset($this->collection[$position])) {
            throw new \OutOfBoundsException(sprintf('Invalid seek position: %s', $position));
        }
        $this->position = $position;
    }
}
