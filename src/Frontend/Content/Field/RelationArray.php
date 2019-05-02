<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

use Studio24\Frontend\Content\PageCollection;
use Studio24\Frontend\Exception\ContentFieldException;

/**
 * Relation field
 *
 * Contains a collection of components, which contain content fields
 *
 * @package Studio24\Frontend\Content\Field
 */
class RelationArray extends ContentField implements \SeekableIterator, \Countable
{
    const TYPE = 'relation_array';

    protected $collection = [];
    protected $position = 0;

    /**
     * Content type of relation
     *
     * @var string
     */
    protected $contentType;

    /**
     * Create flexible content field
     *
     * @param string $name
     * @param string $contentType
     *
     * @throws ContentFieldException
     */
    public function __construct(string $name, string $contentType)
    {
        $this->setName($name);
        $this->setContentType($contentType);
    }

    /**
     * Set content type for child relations
     *
     * @param string $contentType
     * @return RelationArray Fluent interface
     */
    public function setContentType($contentType): RelationArray
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Return content type for child relations
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
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
