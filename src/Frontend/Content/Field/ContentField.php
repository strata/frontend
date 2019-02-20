<?php

namespace Studio24\Frontend\Content\Field;

use Studio24\Frontend\Exception\ContentFieldException;

/**
 * Core content field functionality
 *
 * @package Studio24\Frontend\Content\Field
 */
abstract class ContentField implements ContentFieldInterface
{
    /**
     * Content field name
     * @var string
     */
    protected $name;

    /**
     * Return content field type
     *
     * @return string
     */
    public function getType() : string
    {
        return self::TYPE;
    }

    /**
     * Does the content field contain HTML?
     *
     * @return bool
     */
    public function hasHtml() : bool
    {
        return false;
    }

    /**
     * Get content field name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set content field name
     *
     * @param string $name Content field name, can only use a-z, 0-9, underscore _ and dash -
     * @return ContentInterface
     * @throws ContentFieldException
     */
    public function setName(string $name): ContentFieldInterface
    {
        if (!preg_match('/^[a-z0-9_-]+$/i', $name)) {
            throw new ContentFieldException(sprintf('Invalid content field name: %s', $name));
        }
        $this->name = $name;
        return $this;
    }
}
