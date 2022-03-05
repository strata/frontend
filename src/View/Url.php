<?php

declare(strict_types=1);

namespace Strata\Frontend\View;

/**
 * URL utility class
 */
class Url
{
    /**
     * Generate a URL safe slug from a string
     *
     * Usage:
     * {{ 'My name is Earl' | slugify }}
     *
     * Returns:
     * my-name-is-earl
     *
     * @param $string
     * @return string
     */
    public static function slugify($string): string
    {
        // Filter
        $string = mb_strtolower($string, 'UTF-8');
        $string = strip_tags($string);
        $string = preg_replace('/\s/', '-', $string);
        $string = preg_replace('/[-]+/', '-', $string);

        // Sanitise
        $string = filter_var($string, FILTER_SANITIZE_URL);

        // Replace anything that isn't a unicode letter, number or dash -
        $string = preg_replace('/[^\p{L}\p{N}-]+/', '', $string);

        return $string;
    }

    /**
     * Return URL as a HTTP/S compliant URL for use in hyperlinks
     *
     * If a host is detected the scheme is auto-added if it does not exist (defaults to http)
     *
     * @param string $url URL to fix
     * @param string $scheme The default scheme to use (defaults to https)
     * @return string
     */
    public static function fixUrl(string $url, $scheme = 'https'): string
    {
        $parts = parse_url($url);
        $url = '';

        // Default to $scheme if scheme not set
        if (isset($parts['host']) && !isset($parts['scheme'])) {
            $parts['scheme'] = $scheme;
        }

        // A URL without a scheme looks like a relative URL and not a host (e.g. domain.com/path)
        if (!isset($parts['host']) && isset($parts['path'])) {
            $possibleHost = explode('/', $parts['path']);
            $possibleHost = $possibleHost[0];

            if (filter_var($possibleHost, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false) {
                $parts['scheme'] = $scheme;
                $parts['host'] = $possibleHost;
                $parts['path'] = str_replace($possibleHost, '', $parts['path']);
            }
        }

        if (isset($parts['host'])) {
            $url .= $parts['scheme'] . '://';
            if (isset($parts['user']) || isset($parts['pass'])) {
                $url .= $parts['user'] . ':' . $parts['pass'] . '@';
            }
            $url .= $parts['host'];
            if (isset($parts['port'])) {
                $url .= ':' . $parts['port'];
            }
        }
        if (isset($parts['path'])) {
            $url .= $parts['path'];
        }
        if (isset($parts['query'])) {
            $url .= '?' . $parts['query'];
        }
        if (isset($parts['fragment'])) {
            $url .= '#' . $parts['fragment'];
        }

        return $url;
    }

    /**
     * Strip domain part of URL so URL is relative to current domain
     *
     * @param string $url
     * @return string
     */
    public static function relativeUrl(string $url): string
    {
        $urlParts = parse_url($url);
        $url = $urlParts['path'];
        if (isset($urlParts['query'])) {
            $url .= '?' . $urlParts['query'];
        }
        if (isset($urlParts['fragment'])) {
            $url .= '#' . $urlParts['fragment'];
        }
        return $url;
    }

    /**
     * Strip trailing slash from URIs if they exist
     *
     * @param string $uri
     * @return string
     */
    public static function removeTrailingSlash(string $uri): string
    {
        if (substr($uri, -1, 1) === '/') {
            return substr($uri, 0, -1);
        }
        return $uri;
    }

    /**
     * Strip trailing slash from URIs if they exist
     *
     * @param string $uri
     * @return string
     */
    public static function addTrailingSlash(string $uri): string
    {
        if (substr($uri, -1, 1) !== '/') {
            return $uri . '/';
        }
        return $uri;
    }
}
