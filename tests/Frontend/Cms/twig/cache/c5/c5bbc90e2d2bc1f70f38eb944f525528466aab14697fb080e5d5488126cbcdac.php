<?php

/* pagination.html.twig */
class __TwigTemplate_0d74be287f4d69e5c6f3e7a94dbe582992d94e5652f9f4897fa58b49f4f1b60b extends Twig_Template
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
