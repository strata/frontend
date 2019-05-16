<?php

namespace Studio24\Frontend\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig custom functions and filters
 *
 * To use add this to your services.yaml:

    # Register Frontend Twig helpers
    Studio24\Frontend\Twig\FrontendExtension:
      tags: ['twig.extension']

 * @package Studio24\Frontend\Twig
 */
class FrontendExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return array(
            new TwigFunction('slugify', [$this, 'slugify']),
            new TwigFunction('fix_url', [$this, 'fixUrl']),
        );
    }

    /**
     * Convert a string into a URL safe slug
     *
     * E.g. convert: My name is Earl
     * into: my-name-is-earl
     *
     * @param $string
     * @return string
     */
    public function slugify($string): string
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
     * @param string $scheme The default scheme to use (defaults to http)
     * @return string
     */
    public function fixUrl(string $url, $scheme = 'http'): string
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

    public function buildRevisionFilter()
    {
        // @todo Add build revision to CSS, e.g. {{ 'css' | build_revision }}
        // Outputs: css?r=512
        return "TEST";
    }
}
