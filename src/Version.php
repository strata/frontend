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
        if (class_exists('\Composer\InstalledVersions')) {
            if (InstalledVersions::isInstalled(self::PACKAGE)) {
                return InstalledVersions::getPrettyVersion(self::PACKAGE);
            }
        }
        return null;
    }

    /**
     * Return the user agent string to use with HTTP requests
     *
     * @return string
     */
    public static function getUserAgent(): string
    {
        $version = self::getVersion();
        $version = $version ? '/' . $version : '';
        return 'Strata_Frontend' . $version . ' (+https://github.com/strata/frontend)';
    }
}
