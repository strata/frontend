<?php

namespace Studio24\Frontend\Content\Field;

class ContentFieldCollection implements \ArrayAccess, \SeekableIterator, \Countable
{
    protected $collection = [];
    protected $key;

    /**
     * Add an item to the collection
     *
     * @param ContentFieldInterface $item
     * @return ContentFieldCollection Fluent interface
     */
    public function addItem(ContentFieldInterface $item) : ContentFieldCollection
    {
        $this->collection[$item->getName()] = $item;
        return $this;
    }

    /**
     * @return ContentFieldInterface
     */
    public function current() : ContentFieldInterface
    {
        return $this->collection[$this->key];
    }

    public function next()
    {
        $keys = $this->getKeys();
        foreach ($keys as $num => $key) {
            if ($this->key === $key) {
                $this->key = $keys[$num + 1];
            }
        }
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return isset($this->array[$this->key]);
    }

    public function rewind()
    {
        $this->key = $this->getKeys()[0];
    }

    /**
     * Return current collection array keys
     *
     * @return array
     */
    public function getKeys() : array
    {
        return array_keys($this->collection);
    }

    public function offsetExists($offset)
    {
        return isset($this->collection[$offset]);
    }

    /**
     * @param mixed $offset
     * @return ContentFieldInterface
     */
    public function offsetGet($offset) : ContentFieldInterface
    {
        return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    public function count() : int
    {
        return count($this->collection);
    }

    public function seek($position)
    {
        if (!isset($this->collection[$position])) {
            throw new \OutOfBoundsException(sprintf('Invalid collection key: %s', $position));
        }
        $this->key = $position;
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
