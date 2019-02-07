<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Flexible content field
 *
 * Contains a collection of components, which contain content fields
 *
 * @package Studio24\Frontend\Content\Field
 */
class FlexibleContent extends ContentField
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
     * @throws \Studio24\Exception\ContentFieldException
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
    public function addComponent(Component $item) : FlexibleContent
    {
        $this->components->addItem($item);
        return $this;
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
    public function __toString() : string
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
