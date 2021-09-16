<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* comunes/paginacion.php */
class __TwigTemplate_9b79ea08cba2f391a6dab8a4bf04840ba0cc3ac520c22bd7d4f4fddd734c8ac1 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "comunes/paginacion.php"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "comunes/paginacion.php"));

        // line 1
        echo "<div class=\"row\">
    <div class=\"col12 mt-3\">
        <ul class=\"pagination pagination-md justify-content-center\">
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\"><i class=\"fas fa-angle-left\"></i></a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\">1</a></li>
            <li class=\"page-item active\"><a class=\"page-link\" href=\"#\">2</a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\">3</a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\">4</a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\">5</a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\"><i class=\"fas fa-angle-right\"></i></a></li>
        </ul>\t
    </div>
</div>";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "comunes/paginacion.php";
    }

    public function getDebugInfo()
    {
        return array (  43 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<div class=\"row\">
    <div class=\"col12 mt-3\">
        <ul class=\"pagination pagination-md justify-content-center\">
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\"><i class=\"fas fa-angle-left\"></i></a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\">1</a></li>
            <li class=\"page-item active\"><a class=\"page-link\" href=\"#\">2</a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\">3</a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\">4</a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\">5</a></li>
            <li class=\"page-item\"><a class=\"page-link\" href=\"#\"><i class=\"fas fa-angle-right\"></i></a></li>
        </ul>\t
    </div>
</div>", "comunes/paginacion.php", "/Users/oscarr.rodrigo/Proyectos/Proyectos Web/fincatech/api/v1/templates/comunes/paginacion.php");
    }
}
