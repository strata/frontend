<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

use Strata\Frontend\Content\Pagination\PaginationInterface;

class PageCollection implements \SeekableIterator, \Countable
{
    protected $collection = [];
    protected $position = 0;

    /**
     * @var PaginationInterface
     */
    protected $pagination;

    /**
     * Metadata
     *
     * @var Metadata
     */
    protected $metadata;

    public function __construct(PaginationInterface $pagination)
    {
        $this->pagination = $pagination;
        $this->metadata = new Metadata();
    }

    /**
     * Add an item to the collection
     *
     * @param Page $item
     * @return PageCollection Fluent interface
     */
    public function addItem(Page $item): PageCollection
    {
        $this->collection[] = $item;
        return $this;
    }

    public function getPagination(): PaginationInterface
    {
        return $this->pagination;
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    /**
     * @return Page
     */
    public function current(): Page
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
