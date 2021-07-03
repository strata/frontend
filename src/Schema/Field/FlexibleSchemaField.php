<?php

declare(strict_types=1);

namespace Strata\Frontend\Schema\Field;

use Strata\Frontend\Content\Field\FieldType;
use Strata\Frontend\Content\Field\FlexibleContent;
use Strata\Frontend\Collection\ArrayAccessTrait;
use Strata\Frontend\Schema\ContentFieldCollection;

/**
 * Represents a content field definition (e.g. title field)
 *
 * This contains a collection of content blocks, which contain a collection of content fields
 *
 * @package Strata\Frontend\PageRepositoryMapper
 */
class FlexibleSchemaField extends \ArrayIterator implements SchemaFieldInterface
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
    protected $type = FieldType::FLEXIBLE_CONTENT;

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
     * @return FlexibleSchemaField Fluent interface
     */
    public function setName(string $name): FlexibleSchemaField
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

    public function getOptions(): array
    {
        return [];
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
     * @return FlexibleSchemaField
     * @throws \Strata\Frontend\Exception\ConfigParsingException
     */
    public function addComponents(array $components): FlexibleSchemaField
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
     * @return FlexibleSchemaField Fluent interface
     */
    public function addItem(ContentFieldCollection $item): FlexibleSchemaField
    {
        $this->offsetSet($item->getName(), $item);
        return $this;
    }

    /**
     * Does a flexible content field exist?
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        return $this->offsetExists($name);
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
     * @param string $key
     * @return ContentFieldCollection
     */
    public function offsetGet($key): ContentFieldCollection
    {
        return parent::offsetGet($key);
    }
}
