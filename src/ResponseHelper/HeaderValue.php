<?php

declare(strict_types=1);

namespace Strata\Frontend\ResponseHelper;

/**
 * Simple class to manage header values
 */
class HeaderValue
{
    private bool $replace;
    private $value;

    public function __construct($value, bool $replace = false)
    {
        $this->setValue($value);
        $this->setReplace($replace);
    }

    /**
     * @return bool
     */
    public function isReplace(): bool
    {
        return $this->replace;
    }

    /**
     * @param bool $replace
     */
    public function setReplace(bool $replace): void
    {
        $this->replace = $replace;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
