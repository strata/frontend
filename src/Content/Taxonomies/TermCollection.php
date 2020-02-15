<?php

namespace Studio24\Frontend\Content\Taxonomies;

//@todo setBaseUrls function similar to that of the Menus class to swap API domain for app domain in term links
class TermCollection implements \SeekableIterator, \Countable
{
    protected $collection = [];
    protected $position = 0;

    /**
     * Add an item to the collection
     *
     * @param Term $item Term
     * @return TermCollection
     */
    public function addItem(Term $item): TermCollection
    {
        $this->collection[] = $item;
        return $this;
    }

    /**
     * @return Term
     */
    public function current(): Term
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
