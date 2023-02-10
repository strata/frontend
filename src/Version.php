<?php

declare(strict_types=1);

namespace Strata\Frontend;

use Composer\InstalledVersions;

final class Version
{
    const PACKAGE = 'strata/frontend';

    /**
     * Return current version of Strata Data
     *
     * Requires Composer 2, or returns null if not found
     *
     * @return string|null
     */
    public static function getVersion(): ?string
    {
        if (
            !class_exists(InstalledVersions::class)
            || !InstalledVersions::isInstalled(self::PACKAGE)
        ) {
            return null;
        }

        return InstalledVersions::getPrettyVersion(self::PACKAGE);
    }

    /**
     * Return the user agent string to use with HTTP requests
     *
     * @return string
     */
    public static function getUserAgent(): string
    {
        $version = self::getVersion();

        return sprintf(
            'Strata_Frontend%s (+https://github.com/strata/frontend)',
            is_string($version) ? '/' . $version : ''
        );
    }
}
