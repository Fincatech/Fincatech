<?php

namespace HappySoftware\Controller\Traits;

trait ViewTrait{

    public function InitView()
    {
        //  Hay que comprobar donde está el usuario

        $this->renderView();
    }

    /** Obtiene el contenedor que le corresponde para el main */
    public function getContainerView()
    {
        global $appSettings;
        global $App;

        //  Obtengo el nombre del controlador
        require_once(ABSPATH.'views/' . security[$this->getUserRol()]['dashboard'] . '/dashboard.php');
    }

    public function renderView()
    {
        global $appSettings;
        global $App;

        $rol = $this->getUserRol();

        //  Apertura de página, metas y varios
        include_once(ABSPATH.'views/comunes/header.php');

        //  Menú lateral
        require_once(ABSPATH.'views/' . security[$this->getUserRol()]['dashboard'] . '/menulateral.php');

        //  Contenedor de la aplicación
        include_once(ABSPATH.'views/comunes/container.php');

        //  Incluimos todos los js configurados para la vista
        $this->addJSModulesByRole();

        include_once(ABSPATH.'views/comunes/closer.php');

    }

    /** Inyecta los js del módulo según el rol del usuario */
    private function addJSModulesByRole()
    {
        for($x = 0; $x < count(security[$this->getUserRol()]['js']); $x++)
        {
            $this->addJS( security[$this->getUserRol()]['js'][$x], 1 );
        }
    }

    /** Incluye un fichero JS */
    public function addJS($nombre, $version)
    {
        echo '<script type="text/javascript" src="'. ASSETS_JS . $nombre . (APPENV=="dev" ? "" : "min") . '.js"></script>';
    }

    /** Incluye un fichero CSS */
    public static function addCSS($name, $version)
    {
        echo '<link href="'  . ASSETS_CSS . $name . ".". (APPENV=="dev" ? "" : "min") . '.css?v='.$version .'" rel="stylesheet">';
    }

}

