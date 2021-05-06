<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Exception\ContentFieldException;

/**
 * Flexible content field
 *
 * Contains a collection of components, which contain content fields
 *
 * @package Strata\Frontend\Content\Field
 */
class FlexibleContent extends ContentField implements \SeekableIterator, \Countable
{
    const TYPE = 'flexible';

    /**
     * Collection of components
     *
     * @var ComponentCollection
     */
    protected $components;

    /**
     * Create flexible content field
     *
     * @param string $name
     *
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name)
    {
        $this->setName($name);
        $this->components = new ComponentCollection();
    }

    /**
     * Add component (set of content fields)
     *
     * @param Component $item
     * @return FlexibleContent
     */
    public function addComponent(Component $item): FlexibleContent
    {
        $this->components->addItem($item);
        return $this;
    }

    /**
     * @return Component
     */
    public function current(): Component
    {
        return $this->components->current();
    }

    public function next()
    {
        $this->components->next();
    }

    public function key()
    {
        return $this->components->key();
    }

    public function valid()
    {
        return $this->components->valid();
    }

    public function rewind()
    {
        $this->components->rewind();
    }

    public function count(): int
    {
        return $this->components->count();
    }

    public function seek($position)
    {
        $this->components->seek($position);
    }

    /**
     * Return subset of flexible content that matches the component name
     *
     * @param string $name Component name to return
     * @return ComponentCollection
     */
    public function get(string $name): ComponentCollection
    {
        $components = new ComponentCollection();

        foreach ($this->components as $item) {
            if ($item->getName() === $name) {
                $components->addItem($item);
            }
        }

        return $components;
    }

    /**
     * Return collection of components
     *
     * @return Collection
     */
    public function getValue(): ComponentCollection
    {
        return $this->components;
    }

    /**
     * Return string representation of content field
     *
     * @return string
     */
    public function __toString(): string
    {
        $content = '';

        if (count($this->components) >= 1) {
            foreach ($this->components as $child) {
                $content .= $child->__toString();
            }
        }

        return $content;
    }
}
