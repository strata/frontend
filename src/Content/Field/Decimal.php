<?php
declare(strict_types=1);

namespace Studio24\Frontend\Content\Field;

use Studio24\Frontend\Exception\ContentFieldException;

/**
 * Decimal content field
 *
 * @package Studio24\Frontend\Content\Field
 */
class Decimal extends ContentField
{
    const TYPE = 'decimal';

    /**
     * Short versions of rounding mode
     */
    const ROUND_UP      = 'up';
    const ROUND_DOWN    = 'down';
    const ROUND_EVEN    = 'even';
    const ROUND_ODD     = 'odd';

    protected $number;

    protected $precision = 2;

    /**
     * Allowed rounding modes
     *
     * @var array
     */
    protected $roundModes = [
        self::ROUND_UP    => PHP_ROUND_HALF_UP,
        self::ROUND_DOWN  => PHP_ROUND_HALF_DOWN,
        self::ROUND_EVEN  => PHP_ROUND_HALF_EVEN,
        self::ROUND_ODD   => PHP_ROUND_HALF_ODD
    ];

    /**
     * Default rounding mode
     *
     * @var string
     */
    protected $round = self::ROUND_UP;

    /**
     * Create text content field
     *
     * @param string $name
     * @param $number
     * @param int $precision
     * @param $round Rounding rules for decimal places
     *
     * @throws \Studio24\Frontend\Exception\ContentFieldException
     */
    public function __construct(string $name, $number, $precision = null, $round = null)
    {
        $this->setName($name);
        $this->setNumber($number);
        if ($round !== null) {
            $this->setRounding($round);
        }

        if ($precision !== null) {
            $this->setPrecision($precision);
        }
    }

    /**
     * Set number
     *
     * Casts number to a float
     *
     * @param mixed $number
     * @return Number
     */
    public function setNumber($number): Decimal
    {
        if (is_numeric($number)) {
            $this->number = (float) round($number, $this->precision, $this->getRounding());
        }

        return $this;
    }

    /**
     * Set rounding rules for the decimal number
     *
     * @see https://www.php.net/round
     * @param $round
     * @throws ContentFieldException
     */
    public function setRounding($round)
    {
        $allowed = array_keys($this->roundModes);
        if (!in_array($round, $allowed)) {
            throw new ContentFieldException('Invalid rounding mode, you must pass one of: ' . implode(', ', $allowed));
        }
        $this->round = $round;
    }

    /**
     * Return rounding mode for this decimal
     *
     * This will return the PHP constant used in round(), e.g. PHP_ROUND_HALF_UP
     *
     * @see https://www.php.net/round
     * @return int
     */
    public function getRounding(): int
    {
        return $this->roundModes[$this->round];
    }

    /**
     * Set the number of decimal places to round to
     *
     * @param int $precision
     */
    public function setPrecision(int $precision)
    {
        $this->precision = $precision;
    }

    /**
     * Return number of decimal places for this number
     *
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * Return content
     *
     * @return int
     */
    public function getValue(): float
    {
        return $this->number;
    }

    /**
     * Return string representation of content field
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
