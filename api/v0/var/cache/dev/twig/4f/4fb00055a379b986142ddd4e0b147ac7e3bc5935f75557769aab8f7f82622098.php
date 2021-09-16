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

/* comunidades/listado.php */
class __TwigTemplate_98958b6b7adc212eca12e2640eb274ae6dd1cc1a64855a9b65a4c911498f7ebf extends Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "comunidades/listado.php"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "comunidades/listado.php"));

        // line 1
        echo "<table class=\"table table-hover my-0 listadoComunidadesDashboard\">
    <thead>
        <tr>
            <th>Código</th>
            <th class=\"d-table-cell\">Nombre comunidad</th>
            <th class=\"d-table-cell text-center\">Documentos Verificados</th>
            <th class=\"d-table-cell text-center\">Documentos Pendientes de subir</th>
            <th class=\"d-table-cell text-center\">Documentos Pendientes de verificar</th>
            <th class=\"d-table-cell text-center\">Fecha de alta</th>
            <th class=\"d-table-cell\"></th>
        </tr>
    </thead>
    <tbody>

    <?php foreach( \$datos as \$dato ): ?>

        <tr>
            <td><?= \$dato['codigo'] ?></td>
            <td class=\"d-table-cell\"><?= \$dato['nombre'] ?></td>
            <td class=\"d-table-cell text-center\"><?php //var_dump(\$dato['created']); ?></td>
            <td class=\"d-table-cell text-center\"><span class=\"badge bg-warning\">10</span></td>
            <td class=\"d-table-cell text-center\"><span class=\"badge bg-warning\">10</span></td>
            <td class=\"d-table-cell text-center\"><?php echo date('d/m/Y', strtotime(\$dato['created']['date'])) ?></td>
            <td class=\"d-table-cell text-left\">
                <a href=\"javascript:void(0)\" class=\"btnVerComunidad\" data-nombre=\"<?= \$dato['nombre'] ?>\" data-id=\"<?= \$dato['id'] ?>\"><i data-feather=\"eye\" class=\"text-info\"></i></a>
                <a href=\"comunidad/<?php echo \$dato['id']; ?>\" class=\"btnEditarComunidad\" data-id=\"<?= \$dato['id'] ?>\"><i data-feather=\"edit\" class=\"text-success\"></i></a>
                <a href=\"javascript:void(0);\" class=\"btnEliminarComunidad dd<?= \$dato['id'] ?>\" data-id=\"<?= \$dato['id'] ?>\" data-nombre=\"<?= \$dato['nombre'] ?>\"><i data-feather=\"trash-2\" class=\"text-danger\"></i></a>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "comunidades/listado.php";
    }

    public function getDebugInfo()
    {
        return array (  43 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<table class=\"table table-hover my-0 listadoComunidadesDashboard\">
    <thead>
        <tr>
            <th>Código</th>
            <th class=\"d-table-cell\">Nombre comunidad</th>
            <th class=\"d-table-cell text-center\">Documentos Verificados</th>
            <th class=\"d-table-cell text-center\">Documentos Pendientes de subir</th>
            <th class=\"d-table-cell text-center\">Documentos Pendientes de verificar</th>
            <th class=\"d-table-cell text-center\">Fecha de alta</th>
            <th class=\"d-table-cell\"></th>
        </tr>
    </thead>
    <tbody>

    <?php foreach( \$datos as \$dato ): ?>

        <tr>
            <td><?= \$dato['codigo'] ?></td>
            <td class=\"d-table-cell\"><?= \$dato['nombre'] ?></td>
            <td class=\"d-table-cell text-center\"><?php //var_dump(\$dato['created']); ?></td>
            <td class=\"d-table-cell text-center\"><span class=\"badge bg-warning\">10</span></td>
            <td class=\"d-table-cell text-center\"><span class=\"badge bg-warning\">10</span></td>
            <td class=\"d-table-cell text-center\"><?php echo date('d/m/Y', strtotime(\$dato['created']['date'])) ?></td>
            <td class=\"d-table-cell text-left\">
                <a href=\"javascript:void(0)\" class=\"btnVerComunidad\" data-nombre=\"<?= \$dato['nombre'] ?>\" data-id=\"<?= \$dato['id'] ?>\"><i data-feather=\"eye\" class=\"text-info\"></i></a>
                <a href=\"comunidad/<?php echo \$dato['id']; ?>\" class=\"btnEditarComunidad\" data-id=\"<?= \$dato['id'] ?>\"><i data-feather=\"edit\" class=\"text-success\"></i></a>
                <a href=\"javascript:void(0);\" class=\"btnEliminarComunidad dd<?= \$dato['id'] ?>\" data-id=\"<?= \$dato['id'] ?>\" data-nombre=\"<?= \$dato['nombre'] ?>\"><i data-feather=\"trash-2\" class=\"text-danger\"></i></a>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>", "comunidades/listado.php", "/Users/oscarr.rodrigo/Proyectos/Proyectos Web/fincatech/api/v1/templates/comunidades/listado.php");
    }
}
