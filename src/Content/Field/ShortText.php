<?php

declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Short text content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class ShortText extends ContentField
{
    const TYPE = 'text';

    protected $content = '';
    
    /**
     * Create text content field
     *
     * @param string $name
     * @param string $content
     *
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, string $content)
    {
        $this->setName($name);
        $this->setContent($content);
    }

    /**
     * Set content
     *
     * Content should be short with no line returns
     *
     * @param string $content
     * @return PlainText
     */
    public function setContent(string $content): ShortText
    {
        $content = preg_replace('/\R/', '', $content);
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
        return $this->getValue();
    }
}
