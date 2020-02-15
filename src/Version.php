<?php

declare(strict_types=1);

namespace Studio24\Frontend;

class Version
{
    const VERSION = '0.5.0';

    /**
     * Return version string
     *
     * @return string
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Return the user agent string to use with HTTP requests
     *
     * @return string
     */
    public static function getUserAgent(): string
    {
        return "S24_Frontend/" . self::VERSION . ' (https://github.com/studio24/frontend)';
    }
}
