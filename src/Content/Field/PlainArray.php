<?php

declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Array field, keys can be integers or strings, values can only be numeric of strings (no arrays or objects)
 *
 * @package Studio24\Frontend\Content\Field
 */
class PlainArray extends ContentField
{
    const TYPE = 'plainarray';

    protected $content = array();

    /**
     * Create plain array content field
     *
     * @param string $name
     * @param array $content
     *
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, array $content)
    {
        $this->setName($name);
        $this->setContent($content);
    }

    /**
     * Set content
     *
     * @param array $content
     * @return PlainArray
     */
    public function setContent(array $content): PlainArray
    {
        if (!is_array($content)) {
            return $this;
        }

        if (empty($content)) {
            return $this;
        }

        foreach ($content as $key => $value) {
            if (!(is_int($key) || is_string($key))) {
                continue;
            }

            if (!(is_numeric($value) || is_string($value) || is_bool($value))) {
                continue;
            }

            $new_key = (is_numeric($key)) ? (int) $key : (string) $key;
            $new_value = (is_bool($value)) ? (int) $value : $value;

            $this->content[$new_key] = $new_value;
        }

        return $this;
    }

    /**
     * Return content
     *
     * @return array
     */
    public function getValue(): array
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
        $contentString = '';
        if (empty($this->content)) {
            return $contentString;
        }

        foreach ($this->content as $item) {
            $contentString .= (string) $item . '. ';
        }

        $contentString = substr($contentString, 0, -1);

        return $contentString;
    }
}
