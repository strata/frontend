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
class FlexibleField extends \ArrayIterator implements FieldInterface
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
     * @return FlexibleField Fluent interface
     */
    public function setName(string $name): FlexibleField
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $name
     * @return |null
     */
    public function getOption(string $name)
    {
        return null;
    }

    /**
     * @param string $name
     * @return |null
     */
    public function hasOption(string $name): bool
    {
        return false;
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
     * @return FlexibleField
     * @throws \Studio24\Frontend\Exception\ConfigParsingException
     */
    public function addComponents(array $components): FlexibleField
    {
        foreach ($components as $name => $contentFields) {
            $block = new ContentFieldCollection($name);

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
     * @param ContentFieldCollection $item
     * @return FlexibleField Fluent interface
     */
    public function addItem(ContentFieldCollection $item): FlexibleField
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Return current item
     *
     * @return ContentFieldCollection
     */
    public function current(): ContentFieldCollection
    {
        return parent::current();
    }

    /**
     * Return item by key
     *
     * @param string $index
     * @return ContentFieldCollection
     */
    public function offsetGet($index): ContentFieldCollection
    {
        return parent::offsetGet($index);
    }
}
