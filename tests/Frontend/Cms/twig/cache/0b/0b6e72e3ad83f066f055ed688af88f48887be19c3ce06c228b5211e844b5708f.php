<?php

/* metadata.html.twig */
class __TwigTemplate_66719c2b2ee293427fac0aaa92b311f4525df53c3d445fe0c98161ca4e2a18ae extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["metadata"] ?? null), "my_title", []), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "metadata.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "metadata.html.twig", "/Users/mbuckland/Sites/frontend/tests/Frontend/Cms/twig/templates/metadata.html.twig");
    }
}
