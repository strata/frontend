<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

/**
 * Date content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Date extends ContentField
{
    const TYPE = 'date';

    /**
     * Date
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Create date content field
     *
     * @param string $name Content field name
     * @param null $date Date, see valid date formats https://secure.php.net/manual/en/datetime.formats.date.php
     * @param null $format Date format
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, $date = null, $format = null)
    {
        $this->setName($name);

        if ($date !== null) {
            if ($date instanceof \DateTime) {
                $this->setDate($date);
            } elseif (is_string($date) && ($format !== null)) {
                $this->setDate(\DateTime::createFromFormat($format, $date));
            } else {
                $this->setDate(new \DateTime($date));
            }
        }
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Date
     */
    public function setDate(\DateTime $date) : Date
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Return DateTime object
     *
     * @return DateTime
     */
    public function getDateTime() : \DateTime
    {
        return $this->date;
    }

    /**
     * Return date in a specific format
     *
     * @param string $format
     * @return string
     */
    public function format(string $format) : string
    {
        return $this->date->format($format);
    }

    /**
     * Return date in 2019-01-30 format
     *
     * @return string
     */
    public function getDate() : string
    {
        return $this->format('Y-m-d');
    }

    /**
     * Return month, e.g. 1 for Jan, 7 for July
     *
     * @return string
     */
    public function getMonth() : string
    {
        return $this->format('n');
    }

    /**
     * Return date of the month, e.g. 5, 30
     *
     * @return string
     */
    public function getDay() : string
    {
        return $this->format('j');
    }

    /**
     * Return weekday, e.g. 1 for Monday through 7 for Sunday
     *
     * @return string
     */
    public function getWeekday() : string
    {
        return $this->format('N');
    }

    /**
     * Return PHP DateTime object
     *
     * @return \DateTime
     */
    public function getValue() : \DateTime
    {
        return $this->date;
    }

    /**
     * Return string representation of content field
     *
     * Default format: Y-m-d (e.g. 2005-04-15)
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->format('Y-m-d');
    }
}
