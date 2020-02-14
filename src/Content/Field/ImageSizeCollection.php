<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

class ImageSizeCollection implements \SeekableIterator, \Countable
{
    protected $collection = [];
    protected $position = 0;

    /**
     * Add an item to the collection
     *
     * @param ImageSize $item
     * @return ImageSizeCollection Fluent interface
     */
    public function addItem(ImageSize $item) : ImageSizeCollection
    {
        $this->collection[] = $item;
        return $this;
    }

    /**
     * @return ImageSize
     */
    public function current() : ImageSize
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

    public function count() : int
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
