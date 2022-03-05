<?php

declare(strict_types=1);

namespace Strata\Frontend\View;

/**
 * View template functions for content generation
 *
 * Methods should return new content based on input arguments
 */
class ViewFunctions
{
    /**
     * Return a simple full-width staging banner to indicate this is a test environment and not the live site
     *
     * @param TwigEnvironment $env Twig environment
     * @param string $environment Current environment
     * @param string $message Change message that is outputted
     * @param string $prod Production environment, defaults to 'prod'
     * @return string Staging banner, or null if on production
     */
    public function stagingBanner($environment, $message = 'This is the <strong>%s</strong> environment', $prod = 'prod'): ?string
    {
        $helpers = new ViewFilters();
        $tests = new ViewTests();

        if ($tests->isProd($environment, $prod)) {
            return null;
        } else {
            $className = filter_var($environment, FILTER_SANITIZE_STRING);
            $className = $helpers->slugify($className);
            $message = sprintf($message, $environment);
            return <<<EOD
<div class="staging-banner $className">$message</div>
<style>
    .staging-banner {
        width: 100%;
        padding: 0.6em 1em;
        background-color: yellow;
        border-bottom: 1px solid #333;
        color: black;
        font-family: sans-serif;
    }
</style>

EOD;
        }
    }


    /**
     * Check a list of variables, and if one of them isn't empty, then returns true
     *
     * If all variables passed are empty, then returns false
     *
     * Can be used to simplify a long list of checks, e.g.
     *
     * `if ... is not empty or ... is not empty or ... is not empty` in twig
     *
     * @param mixed ...$variables
     * @return bool
     */
    public function notEmpty(...$variables)
    {
        $anyDefined = false;

        foreach ($variables as $variable) {
            if (!empty($variable)) {
                $anyDefined = true;
                break;
            }
        }

        return $anyDefined;
    }

    /**
     * Check a list of variables, and if all of them aren't empty, then returns true
     *
     * If all variables passed are empty, then returns false
     *
     * Can be used to simplify a long list of checks, e.g.
     *
     * `if ... is not empty and ... is not empty and ... is not empty` in twig
     *
     * @param mixed ...$variables
     * @return bool
     */
    public function allNotEmpty(...$variables)
    {
        $allNotEmpty = false;
        $numVariables = count($variables);
        $numVariablesDefined = 0;

        foreach ($variables as $variable) {
            if (!empty($variable)) {
                $numVariablesDefined++;
            }
        }

        if ($numVariables === $numVariablesDefined) {
            $allNotEmpty = true;
        }

        return $allNotEmpty;
    }
}
