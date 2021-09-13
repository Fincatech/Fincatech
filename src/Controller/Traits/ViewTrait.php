<?php

namespace HappySoftware\Controller\Traits;

trait ViewTrait{

    public function InitView()
    {
        //  Hay que comprobar donde está el usuario

        $this->renderView();
    }

    public function renderActionButtons()
    {
        global $appSettings, $App;
        include(ABSPATH.'views/comunes/acciones_form.php');
    }

    /** Comprueba si se ha de renderizar el menú lateral */
    public function renderMenuLateral()
    {
        global $appSettings;
        global $App;

        return ( security[$this->getUserRol()]['menulateral'] == true );
    }

    public function getHeader()
    {
        global $appSettings;
        global $App;    
    }

    /** Obtiene el nombre de la vista que se está cargando para pintarla en el título */
    public function getTitleView()
    {
        global $appSettings;
        return security[$this->getUserRol()]['titulo'];
    }

    /** Obtiene el contenedor que le corresponde para el main */
    public function getContainerView()
    {
        global $appSettings;
        global $App;
        
        //  Obtengo el nombre del controlador
        $includeFile = ABSPATH.'views/';
        
        //  Incluimos las opciones de menú a las que tiene acceso según su rol de usuario
        include(ABSPATH . 'views/' . security[$this->getUserRol()]['dashboard'] . '/menuacciones.php');

        //  Comprobamos qué vista es la que hay que renderizar
        if($this->controllerName == "dashboard")
        {
            $includeFile .= security[$this->getUserRol()]['dashboard'] . '/dashboard';
        }else{

            switch($this->getAction())
            {
                case "get":
                    $iconoAccion = "edit";
                    $includeFile .= $this->getController() . '/form';
                    break;
                case "add":
                    $iconoAccion = "plus-square";
                    $includeFile .= $this->getController() . '/form';
                    break;
                default:
                    $iconoAccion = "list";
                    $App->renderTable("listado" . ucfirst($this->getController()), ucfirst($this->getController()), []); 
                    //$App->renderTable("listadoComunidades", "comunidad", ["Codigo", "Nombre comunidad", "E-mail", "Teléfono", "Doc verificados", "Doc pendientes de subir", "Doc pendientes de verificar", ""]); 
                    $includeFile .= "componentes/listado/listado";
                    break;
            }

            if($this->getAction() == 'get')
            {
                //  Form
                // $includeFile .= $this->getController() . '/form';
            }else{
                //  Listado
                //DEBUG: 
                
                // TODO: Hay que meter esta configuración para el controller
                //  de esa forma, simplemente hay que rellenar la info en el settings
                //  y nos olvidamos de tener que montar un controller por cada entidad
                //  ya que la idea es que venga todo informado desde el back
                //  y dejar automatizado al máximo posible todo
                // $includeFile .= "componentes/listado/listado";
            }
        }
        
        // echo('Controller: ' . $this->getController() . 
        //     " - Action: " . $this->controllerAction . 
        //     " - ID: " . $this->_id .
        //     " - Include: " . $includeFile . '.php');

        require_once( $includeFile . '.php');
    }

    /** Renderiza el icono de menú
     */
    public function renderBotonMenu($titulo, $urlDestino, $destino = null, $imagen = null, $icono = null)
    {
        global $App;
        include(ABSPATH.'views/componentes/boton/icono_boton_menu.php');
    }

    /** Renderiza la vista en función del controller */
    public function renderView()
    {
        global $appSettings;
        global $App;

        $rol = $this->getUserRol();

        //  Apertura de página, metas y varios
        include_once(ABSPATH.'views/comunes/header.php');

        //  Renderiza el menú lateral
        if( $this->renderMenuLateral())
        {
            //  Menú lateral
            require_once(ABSPATH.'views/' . security[$this->getUserRol()]['dashboard'] . '/menulateral.php');
        }

        //  Contenedor de la aplicación
        include_once(ABSPATH.'views/comunes/container.php');     

        //  Incluimos todos los js configurados para la vista
        $this->addJSModulesByRole();

        //  Incluimos el cierre del dom
        include_once(ABSPATH.'views/comunes/closer.php');

    }

    /** Inyecta los js del módulo según el rol del usuario */
    private function addJSModulesByRole()
    {
        if(!isset(security[$this->getUserRol()]['js']))
            return;

        for($x = 0; $x < count(security[$this->getUserRol()]['js']); $x++)
        {
            //$this->addJS( security[$this->getUserRol()]['js'][$x], 1 );
            $this->addJS( security[$this->getUserRol()]['js'][$x], time() );
        }
    }

    /** Incluye un fichero JS */
    public function addJS($nombre, $version)
    {
        echo '<script type="text/javascript" src="'. ASSETS_JS . $nombre . (APPENV=="dev" ? "" : "min") . '.js?v='.$version.'"></script>';
    }

    /** Incluye un fichero CSS */
    public static function addCSS($name, $version)
    {
        echo '<link href="'  . ASSETS_CSS . $name . ".". (APPENV=="dev" ? "" : "min") . '.css?v='.$version .'" rel="stylesheet">';
    }

}

