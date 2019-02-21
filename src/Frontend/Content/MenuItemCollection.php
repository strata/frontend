<?php
/**
 * Created by PhpStorm.
 * User: BenDB
 * Date: 21/02/2019
 * Time: 09:54
 */

namespace Studio24\Frontend\Content;


class MenuItemCollection implements \SeekableIterator, \Countable
{
    protected $collection = [];
    protected $position = 0;

    /**
     * Add an item to the collection
     *
     * @param MenuItem $item MenuItem
     * @return MenuItemCollection Fluent interface
     */
    public function addItem(MenuItem $item) : MenuItemCollection
    {
        $this->collection[] = $item;
        return $this;
    }

    /**
     * @return MenuItem
     */
    public function current() : MenuItem
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
        return isset($this->array[$this->position]);
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