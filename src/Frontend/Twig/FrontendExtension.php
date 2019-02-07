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

    public function test()
    {
        return "TEST";
    }
}
