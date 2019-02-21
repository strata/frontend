<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Number content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Number extends ContentField
{
    const TYPE = 'number';

    protected $number;
    
    /**
     * Create text content field
     *
     * @param string $name
     * @param $number
     *
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, $number)
    {
        $this->setName($name);
        $this->setNumber($number);
    }

    /**
     * Set number
     *
     * Casts number to an integer
     *
     * @param mixed $number
     * @return Number
     */
    public function setNumber($number) : ?Number
    {
        if (is_numeric($number)) {
            $this->number = (int) $number;
        }

        return $this;
    }

    /**
     * Return content
     *
     * @return int
     */
    public function getValue(): int
    {
        return $this->number;
    }

    /**
     * Return string representation of content field
     *
     * @return string
     */
    public function __toString() : string
    {
        return (string) $this->number;
    }
}
