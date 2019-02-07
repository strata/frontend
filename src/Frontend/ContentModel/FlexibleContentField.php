<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

use Studio24\Frontend\Content\Field\FlexibleContent;

/**
 * Represents a content field definition (e.g. title field)
 *
 * This contains a collection of content blocks, which contain a collection of content fields
 *
 * @package Studio24\Frontend\ContentModel
 */
class FlexibleContentField implements ContentFieldInterface, \ArrayAccess, \SeekableIterator, \Countable
{
    /**
     * Content field name
     *
     * @var string
     */
    protected $name;

    /**
     * Content field type
     *
     * @var string
     */
    protected $type = FlexibleContent::TYPE;

    /**
     * Collection of content blocks
     * @var array
     */
    protected $blocks = [];

    /**
     * Content type collection key
     *
     * @var string
     */
    protected $key;

    /**
     * Constructor
     *
     * @param string $name
     * @param array $components
     */
    public function __construct(string $name, array $components = [])
    {
        $this->setName($name);

        if (!empty($components)) {
            $this->addComponents($components);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return FlexibleContentField Fluent interface
     */
    public function setName(string $name): FlexibleContentField
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Add components from an array of data (normally loaded from config file)
     *
     * @param array $components
     * @return FlexibleContentField
     */
    public function addComponents(array $components): FlexibleContentField
    {
        foreach ($components as $name => $contentFields) {
            $block = new ContentBlock($name);

            foreach ($contentFields as $name => $values) {
                $block->addItem($block->parseContentFieldArray($name, $values));
            }

            $this->addBlock($block);
        }

        return $this;
    }

    /**
     * Add an item to the collection
     *
     * @param ContentType $item
     * @return FlexibleContentField Fluent interface
     */
    public function addBlock(ContentBlock $item): FlexibleContentField
    {
        $this->blocks[$item->getName()] = $item;
        return $this;
    }

    /**
     * @return ContentBlock
     */
    public function current() : ContentBlock
    {
        return $this->blocks[$this->key];
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
        return array_keys($this->blocks);
    }

    public function offsetExists($offset)
    {
        return isset($this->blocks[$offset]);
    }

    /**
     * @param mixed $offset
     * @return ContentField
     */
    public function offsetGet($offset) : ContentBlock
    {
        return isset($this->blocks[$offset]) ? $this->blocks[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->blocks[] = $value;
        } else {
            $this->blocks[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->blocks[$offset]);
    }

    public function count() : int
    {
        return count($this->blocks);
    }

    public function seek($position)
    {
        if (!isset($this->blocks[$position])) {
            throw new \OutOfBoundsException(sprintf('Invalid content block key: %s', $position));
        }
        $this->key = $position;
    }
}
