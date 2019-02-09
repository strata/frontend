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
            new TwigFunction('test', array($this, 'test'))
        );
    }

    public function buildRevisionFilter()
    {
        // @todo Add build revision to CSS, e.g. {{ 'css' | build_revision }}
        // Outputs: css?r=512
        return "TEST";
    }
}
