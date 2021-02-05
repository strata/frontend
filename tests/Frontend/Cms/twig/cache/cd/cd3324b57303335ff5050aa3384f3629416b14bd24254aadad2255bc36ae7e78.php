<?php

/* pagination.html.twig */
class __TwigTemplate_16da2dab22ec14ad21a0f6346e1dee17d1a8e50e7185cdbf695783a1e1318ea9 extends Twig_Template
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
        echo "This is page ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "page", []), "html", null, true);
        echo ", displaying results ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "from", []), "html", null, true);
        echo "-";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["pagination"] ?? null), "to", []), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "pagination.html.twig";
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
        return new Twig_Source("", "pagination.html.twig", "/Users/mbuckland/Sites/frontend/tests/Frontend/Cms/twig/templates/pagination.html.twig");
    }
}
