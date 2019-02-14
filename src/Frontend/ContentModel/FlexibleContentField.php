<?php
declare(strict_types=1);

namespace Studio24\Frontend\ContentModel;

use Studio24\Frontend\Content\Field\FlexibleContent;
use Studio24\Frontend\Collection\ArrayAccessTrait;

/**
 * Represents a content field definition (e.g. title field)
 *
 * This contains a collection of content blocks, which contain a collection of content fields
 *
 * @package Studio24\Frontend\ContentModel
 */
class FlexibleContentField extends \ArrayIterator implements ContentFieldInterface
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
            $block = new Component($name);

            foreach ($contentFields as $name => $values) {
                $block->addItem($block->parseContentFieldArray($name, $values));
            }

            $this->addItem($block);
        }

        return $this;
    }

    /**
     * Add an item to the collection
     *
     * @param Component $item
     * @return FlexibleContentField Fluent interface
     */
    public function addItem(Component $item): FlexibleContentField
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Return current item
     *
     * @return Component
     */
    public function current(): Component
    {
        return parent::current();
    }

    /**
     * Return item by key
     *
     * @param string $index
     * @return Component
     */
    public function offsetGet($index): Component
    {
        return parent::offsetGet($index);
    }

}
