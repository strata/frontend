<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

/**
 * Text content field
 *
 * @package Strata\Frontend\Content\Field
 */
class RichText extends ContentField
{
    const TYPE = 'richtext';

    protected $content;

    /**
     * Does the content field contain HTML?
     *
     * @return bool
     */
    public function hasHtml(): bool
    {
        return true;
    }

    /**
     * Create text content field
     *
     * @param string $name
     * @param string $content
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, string $content)
    {
        $this->setName($name);
        $this->setContent($content);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return PlainText
     */
    public function setContent(string $content): RichText
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Return content
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->content;
    }

    /**
     * Return string representation of content field
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
