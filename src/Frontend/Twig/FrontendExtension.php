<?php

namespace Studio24\Frontend\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig custom functions and filters
 *
 * @package Studio24\Frontend\Twig
 */
class FrontendExtension extends AbstractExtension
{

    public function getFunctions()
    {
        return array(
            new TwigFunction('slugify', array($this, 'slugify'))
        );
    }

    /**
     * Convert a string into a URL safe slug
     *
     * E.g. convert: My name is Earl
     * into: my-name-is-earl
     *
     * @param string $string
     * @return string
     */
    public function slugify(string $string): string
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

    public function buildRevisionFilter()
    {
        // @todo Add build revision to CSS, e.g. {{ 'css' | build_revision }}
        // Outputs: css?r=512
        return "TEST";
    }
}
