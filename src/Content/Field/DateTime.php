<?php

declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Date time content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class DateTime extends Date
{
    const TYPE = 'datetime';

    /**
     * Create date time content field
     *
     * @param string $name Content field name
     * @param null $date Date and time, see valid date formats https://secure.php.net/manual/en/datetime.formats.compound.php
     * @param null $format Date format
     *
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, $date = null, $format = null)
    {
        parent::__construct($name, $date, $format);
    }

    /**
     * Return hour in 24-hr format, e.g. 1 for 1am, 15 for 3pm
     *
     * @return string
     */
    public function getHour(): string
    {
        return $this->format('G');
    }

    /**
     * Return minutes with leading zeros, e.g. 05 for 5 mins, 15 for 15 mins
     *
     * @return string
     */
    public function getMinutes(): string
    {
        return $this->format('i');
    }

    /**
     * Return seconds with leading zeros, e.g. 05 for 5 secs, 15 for 15 secs
     *
     * @return string
     */
    public function getSeconds(): string
    {
        return $this->format('s');
    }

    /**
     * Return string representation of content field
     *
     * Default format: DateTime::DATE_ATOM (e.g. 2005-08-15T15:52:01+00:00)
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->format(\DateTime::ATOM);
    }
}
