<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

use Studio24\Frontend\Content\PageCollection;
use Studio24\Frontend\Exception\ContentFieldException;

/**
 * Relation field
 *
 * Contains a collection of relation items
 *
 * @package Studio24\Frontend\Content\Field
 */
class RelationArray extends ContentField implements \SeekableIterator, \Countable
{
    const TYPE = 'relation_array';

    protected $collection = [];
    protected $position = 0;

    /**
     * Create flexible content field
     *
     * @param string $name
     * @param string $contentType
     *
     * @throws ContentFieldException
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }


    /**
     * Add relation item
     *
     * @param Relation $item
     * @return RelationArray Fluent interface
     */
    public function addItem(Relation $item): RelationArray
    {
        $this->collection[] = $item;
        return $this;
    }

    /**
     * @return Relation
     */
    public function current(): Relation
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

    /**
     * Return array of relation items
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
    public function __toString() : string
    {
        $content = '';

        if (count($this->collection) >= 1) {
            foreach ($this->collection as $child) {
                $content .= $child->__toString();
            }
        }

        return $content;
    }
}
