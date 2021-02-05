<?php

/* metadata.html.twig */
class __TwigTemplate_3492b08d1adc1a0f634b54e9293c683a31d3496c30683112e9b7e59fed38ef60 extends Twig_Template
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
