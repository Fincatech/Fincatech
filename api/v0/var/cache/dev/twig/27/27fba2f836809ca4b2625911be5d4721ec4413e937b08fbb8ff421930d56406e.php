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

/* comunidades/listadomenulateral.php */
class __TwigTemplate_d34a8cb0a6dd9c2444491068113132fe418a2c4704ddf9e590a87c0a99d2ed2e extends Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "comunidades/listadomenulateral.php"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "comunidades/listadomenulateral.php"));

        // line 1
        echo "<li class=\"sidebar-header\">Comunidades</li>
<?php foreach(\$datos as \$dato): ?>

    <li class=\"sidebar-item\">
        <a class=\"sidebar-link\" href=\"comunidad/<?php echo \$dato['id']; ?>\">
            <img src=\"assets/img/icon_edificio.png\" class=\"img-responsive feather\">
            <span class=\"align-middle pl-3\"><?php echo \$dato['codigo'] . ' - ' . \$dato['nombre'] ?></span>
        </a>
    </li>

<?php endforeach; ?>";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "comunidades/listadomenulateral.php";
    }

    public function getDebugInfo()
    {
        return array (  43 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<li class=\"sidebar-header\">Comunidades</li>
<?php foreach(\$datos as \$dato): ?>

    <li class=\"sidebar-item\">
        <a class=\"sidebar-link\" href=\"comunidad/<?php echo \$dato['id']; ?>\">
            <img src=\"assets/img/icon_edificio.png\" class=\"img-responsive feather\">
            <span class=\"align-middle pl-3\"><?php echo \$dato['codigo'] . ' - ' . \$dato['nombre'] ?></span>
        </a>
    </li>

<?php endforeach; ?>", "comunidades/listadomenulateral.php", "/Users/oscarr.rodrigo/Proyectos/Proyectos Web/fincatech/api/v1/templates/comunidades/listadomenulateral.php");
    }
}
