<?php

namespace HappySoftware\Controller;

use Firebase\JWT\JWT;
use HappySoftware\Controller\Traits\SecurityTrait;
use HappySoftware\Controller\Traits\ViewTrait;
use HappySoftware\Controller\Traits\TableTrait;
use HappySoftware\Controller\Traits\UtilsTrait;
use Mpdf\Tag\Main;

class MainController{

    use SecurityTrait, TableTrait, UtilsTrait, ViewTrait;

    //  Modo mantenimiento
    public $maintenanceMode = true;
    //  IP's privadas para no activar modo mantenimiento
    private $secureIP = array('87.220.218.115');
    public $isAuthorizedIP = false;

    //  Tiempo estimado de mantenimiento
    private $estimatedMaintenanceTime = '24h';

    private $userRol;
    private $rgpdAceptado;
    private $pageTitle;
    private $controllerName;
    private $controllerAction;
    private $controllerRoute;
    private $_id;
    private $_usuarioId;

    //  Esta variable se utiliza para comprobar si el segundo parámetro del controller
    //  es una acción propiamente dicha, si no, indica una subruta
    //  FIXME: Esto debería de ir en el middleware
    private $aceptedActions = Array('create', 'add', 'list'); 

    public function __construct()
    {
        //  Incluimos el trait de seguridad
        require_once(ABSPATH."src/Controller/Traits/SecurityTrait.php");
    }

    /** Comprueba si el usuario autenticado es de tipo sudo */
    public function isSudo()
    {
        return ( $this->getUserRol() == 'ROLE_SUDO' );
    }

    /** Comprueba si el usuario autenticado es un admin de fincas */
    public function isAdminFincas()
    {
        return ( $this->getUserRol() == 'ROLE_ADMINFINCAS' );
    }

    public function isDPD()
    {
        return ( $this->getUserRol() == 'ROLE_DPD' );
    }

    /** Comprueba si el usuario autenticado es un contratista */
    public function isContratista()
    {
        return ( $this->getUserRol() == 'ROLE_CONTRATISTA' ); 
    }

    /** Devuelve si el usuario tiene el RGPD aceptado */
    public function tieneRGPDAceptado()
    {
        return $this->rgpdAceptado;
    }

    public function getFullControllerRoute()
    {
        return $this->controllerRoute;
    } 

    private function setFullControllerRoute($value)
    {
        $this->controllerRoute = $value;
    } 

    public function getUserRol()
    {
        return $this->userRol;
    }

    public function getUserId()
    {
        return $this->_usuarioId;
    }

    /** Obtiene la url a la que debe dirigir */
    public function composeURLController($controllerName)
    {
        $urlDestino = $controllerName;
        //$urlDestino = $this->getController();
        return $urlDestino;

    }

    public function getAction()
    {
        return $this->controllerAction;
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * Devuelve el nombre del controlador
     */
    public function getController()
    {
        return $this->controllerName;
    }

    /** Establece el nombre del controlador, la acción y el ID */
    public function setController($value)
    {
        $controllerInfo = explode("/", $value);

        //  El primer parámetro es el nombre del controlador
        $this->controllerName = $controllerInfo[0];

        //  El segundo es la acción, ID solicitado o vista fuera de la arquitectura por defecto
        if(count($controllerInfo) > 1)
        {
            //  Comprobamos si es un entero o una acción
            if(is_numeric($controllerInfo[1]))
            {
                $this->_id = $controllerInfo[1];
                $this->controllerAction = "get";
            }else{
                $this->_id = null;
                $this->controllerAction = $controllerInfo[1];
            }
        }else{
            $this->controllerAction = "list";
        }

        // die($this->controllerAction);

        //  Comprobamos si está en el dashboard y además es un administrador de fincas
        if($this->getController() == 'dashboard')
            $this->controllerAction = '';

        return $this;
    }

    /** Inicializa el Front Controller */
    public function Run()
    {
        //  Guardamos toda la url de la petición
            $this->setFullControllerRoute($_GET['route']);

            if(empty($_GET['route']))
            {
                // $controller = ($this->isLogged() ? 'dashboard' : 'login');
                $controller = ('login');
                $this->setController( $controller );
            }else{
                $this->setController( $_GET['route'] );
            }

            $this->isAuthorizedIP = $this->IsAuthorizedIPMaintenanceMode();

        //  Comprobamos si el modo mantenimiento está activado
            if($this->maintenanceMode === true && $this->getController() != 'login' && !$this->isAuthorizedIP){
                MainController::ShowMaintenancePage();
            }

        //  Comprobamos la seguridad
            if(!$this->isLogged() && $this->getController() != 'login')
            {
                MainController::redirectToLogin();
            }
            
            $resultValidation = $this->checkSecurity();

            if($resultValidation['status'] === true && !empty($resultValidation['data']))
            {
                $userData = $resultValidation['data']->userData;
                $this->userRol = $userData->role;
                $this->rgpdAceptado = $userData->rgpd;
                $this->_usuarioId = $userData->id;
                $this->InitView();
            }else{

                if($this->getController() === 'login')
                {
                    $this->userRol = 'ROLE_LOGIN';
                    $this->InitView();
                
                }else{
                    MainController::redirectToLogin();
                    exit;
                }

            }

    }

    public static function getUserAlias()
    {
        //  Recuperamos de la cookie el tipo de usuario que es para inicializar

        //  Si no hay cookie devolvemos un false para redireccionar

    }

    /**
     * Redirecciona a la página de login
     */
    public static function redirectToLogin()
    {
        header('Location: ' . HOME_URL . 'login');
        exit;
    }

    /**
     * Redirects to Maintenance Page
     */
    public static function ShowMaintenancePage()
    {
        header('Location: ' . HOME_URL );
        exit;
    }

    public function GetLogo()
    {
        //  Comprobamos el dominio que es para poder asignar el logo correspondiente
        $dominio = $_SERVER['SERVER_NAME'];
        $aDominio = explode('.', $dominio);
        $nombreFranquiciado = $aDominio[count($aDominio) - 2];
        return strtolower($nombreFranquiciado);

    }

    public function CurrentMonth()
    {
        $month = date('m');
        $months = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
            ];        
        return ucfirst($months[intval($month)]);
    }

    /**
     * Comprueba si la ip de conexión pertenece a una ip autorizada en modo mantenimiento
     */
    private function IsAuthorizedIPMaintenanceMode()
    {
        $ipConection = $this->GetUserIP();
        return (in_array($ipConection, $this->secureIP));
    }

}