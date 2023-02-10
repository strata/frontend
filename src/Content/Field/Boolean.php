<?php

declare(strict_types=1);

namespace Strata\Frontend\Content\Field;

use Strata\Frontend\Exception\ContentFieldException;

/**
 * Boolean content field
 *
 * @package Strata\Frontend\Content\Field
 */
class Boolean extends ContentField
{
    protected $value;

    /**
     * Create number content field
     *
     * @param string $name
     * @param mixed $value
     * @throws \Strata\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, $value = null)
    {
        $this->setName($name);

        if ($value !== null) {
            $this->setValue($value);
        }
    }

    /**
     * Set boolean value
     *
     * @param mixed $value Boolean or string to represent true (y, yes, 1) or false (n ,no, 0)
     * @return Boolean
     */
    public function setValue($value): Boolean
    {
        if (is_bool($value)) {
            $this->value = $value;
            return $this;
        }
        if (!is_string($value)) {
            if (preg_match('/(y|yes|1)/i', $value)) {
                $this->value = true;
            }
            if (preg_match('/(n|no|0)/i', $value)) {
                $this->value = false;
            }
        }
        if ($value === 1) {
            $this->value = true;
        } else {
            $this->value = false;
        }

        return $this;
    }

    /**
     * Is this content field true?
     *
     * @return bool
     */
    public function true(): bool
    {
        return ($this->value === true);
    }

    /**
     * Is this content field false?
     *
     * @return bool
     */
    public function false(): bool
    {
        return ($this->value === false);
    }

    /**
     * Return boolean value
     *
     * @return bool
     */
    public function getValue(): bool
    {
        return $this->value;
    }

    /**
     * Return string representation of content field
     *
     * @return string
     */
    public function __toString(): string
    {
        return ($this->value) ? 'true' : 'false';
    }
}
