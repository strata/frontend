<?php

/* head.html.twig */
class __TwigTemplate_ebf1bf0564bf27f2dd80d11229eac5cb852ac39db7366f0dc18a60bce2b08f1e extends Twig_Template
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
        echo twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "head", []);
        echo "
";
    }

    public function getTemplateName()
    {
        return "head.html.twig";
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
        return new Twig_Source("", "head.html.twig", "/Users/mbuckland/Sites/frontend/tests/Frontend/Cms/twig/templates/head.html.twig");
    }
}
