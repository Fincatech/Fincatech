<?php

namespace HappySoftware\Controller\Traits;

trait ViewTrait{

    public function InitView()
    {
        //  Hay que comprobar donde está el usuario
        $this->renderAppView();
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
        if( $this->isLogged() && 
            (!$this->maintenanceMode || ($this->maintenanceMode && $this->isAuthorizedIP)))
        {
            if(file_exists(ABSPATH . 'views/' . security[$this->getUserRol()]['folder'] . '/menuacciones.php'))
                include(ABSPATH . 'views/' . security[$this->getUserRol()]['folder'] . '/menuacciones.php');
        }

        $accion = $this->getAction();

        //  Comprobamos qué vista es la que hay que renderizar
        switch(strtolower($this->controllerName))
        {
            case 'dashboard':
                $includeFile .= security[$this->getUserRol()]['folder'] . '/dashboard';
                break;
            case 'login':
                if( ($this->maintenanceMode && !$this->isAuthorizedIP) )
                {
                    $includeFile .= '/maintenance';
                }else{
                    $includeFile .= '/login/login';
                }
                break;
            case 'maintenance':
                $includeFile .= '/maintenance';
                break;
            default:
                switch( $accion )
                {
                    case "get":
                        $iconoAccion = "edit";
                        $includeFile .= $this->getController() . '/form';
                        break;
                    case "add":
                        $iconoAccion = "plus-square";
                        $includeFile .= $this->getController() . '/form';
                        break;
                    case 'list':
                        $iconoAccion = "list";
                        //  Comprobamos si existe el tipo de listado personalizado, si existe, cargamos la info, si no, cargamos el componente principal
                        if( $this->checkIfViewExists($includeFile, $this->getController() . '/' . $accion ))
                        {
                            $includeFile .=  $this->getController() . '/' . $accion;
                        }else{
                            $App->renderTable("listado" . ucfirst($this->getController()), ucfirst($this->getController()), []); 
                            $includeFile .= $this->getListadoRoute();
                        }
                        break;
                    default:
                    
                        //  Es una vista aparte
                        if( $this->checkIfViewExists($includeFile, $this->getController() . '/' . $accion ))
                        {
                            $includeFile .=  $this->getController() . '/' . $accion;
                        }else{
                            //  Mostramos un 404
                            $includeFile .= 'comunes/404';
                        }
                        break;
                }
                
                //     //DEBUG: 
                    
                //     // TODO: Hay que meter esta configuración para el controller
                //     //  de esa forma, simplemente hay que rellenar la info en el settings
                //     //  y nos olvidamos de tener que montar un controller por cada entidad
                //     //  ya que la idea es que venga todo informado desde el back
                //     //  y dejar automatizado al máximo posible todo
                //     // $includeFile .= "componentes/listado/listado";
                // }

                break;
        }
        
//         echo('Controller: ' . $this->getController() . 
//             " - Action: " . $this->controllerAction . 
//             " - ID: " . $this->_id .
//             " - Include: " . $includeFile . '.php');
// die('1');
        require_once( $includeFile . '.php');
    }

    /**
     * Renderiza el icono de menú
     * @param string $titulo
     * @param string $urlDestino
     * @param string $imagen
     * @param string $icono
     */
    public function renderBotonMenu($titulo, $urlDestino, $destino = null, $imagen = null, $icono = null)
    {
        global $App;
        include(ABSPATH.'views/componentes/boton/icono_boton_menu.php');
    }

    public function renderView($viewName, $executeView = false)
    {
    
        global $appSettings;
        global $App;

        try{
            $viewName = strpos($viewName, '.php') === false ? $viewName . '.php' : $viewName;
            //  Validamos que exista la vista
            $viewRoute = ABSPATH.'views/'.$viewName;
            if(!file_exists($viewRoute))
            {
                echo("La vista $viewRoute no existe");
                //throw new \Exception('Vista no encontrada');
            }else{
                include($viewRoute);
            }

        }catch(\Exception $ex){

            die('Vista no encontrada');

        }

    }

    /** Valida si existe ya el fichero de listado o bien cogemos el componente por defecto */
    public function getListadoRoute()
    {
        //  Comprobamos si del modelo que se va a recuperar (es decir, el nombre del controller) hay un fichero de listado
        //  Si no devolvemos el por defecto
        $ruta = 'componentes/listado/listado';
        if(file_exists(ABSPATH . 'views/' . $this->getController() . '/listado.php'))
            $ruta = $this->getController() . '/listado';

        return $ruta;
    }

    private function checkIfViewExists($route, $view)
    {
        return file_exists( $route . $view . '.php' );
    } 

    /** Renderiza la vista en función del controller */
    public function renderAppView()
    {
        global $appSettings;
        global $App;

        $rol = $this->getUserRol();

        //  Si no está autenticado renderizamos la vista del login

        //  Apertura de página, metas y varios
        include_once(ABSPATH.'views/comunes/header.php');

        // //  Renderiza el menú lateral
        if( $this->renderMenuLateral() && $this->isLogged() && 
        (!$this->maintenanceMode || ($this->maintenanceMode && $this->isAuthorizedIP)))
        {
            //  Menú lateral
            require_once(ABSPATH.'views/' . security[$rol]['folder'] . '/menulateral.php');
        }

        //  FIXME:  Meter la apertura del container en un fichero independiente
        //          Llamamos al método getContainerView()
        //          Cerramos el container
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
        echo '<script type="text/javascript" src="'. ASSETS_JS . $nombre . (APPENV=='dev' ? '' : '.min') . '.js?v='.$version.'"></script>' . PHP_EOL;
    }

    /** Incluye un fichero CSS */
    public static function addCSS($name, $version)
    {
        echo '<link href="'  . ASSETS_CSS . $name . ".". (APPENV=="dev" ? "" : "min") . '.css?v='.$version .'" rel="stylesheet">';
    }

}

