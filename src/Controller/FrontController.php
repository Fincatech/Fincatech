<?php

namespace HappySoftware\Controller;

use Firebase\JWT\JWT;
use HappySoftware\Controller\Traits\SecurityTrait;
use HappySoftware\Controller\Traits\ViewTrait;
use HappySoftware\Controller\Traits\TableTrait;

class MainController{

    use SecurityTrait, TableTrait, ViewTrait;

    private $userRol;
    private $pageTitle;
    private $controllerName;
    private $controllerAction;
    private $_id;

    public function __construct()
    {
        //  Incluimos el trait de seguridad
        require_once(ABSPATH."src/Controller/Traits/SecurityTrait.php");
    }

    public function getUserRol()
    {
        return $this->userRol;
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

        //  El segundo es la acción y/o el ID solicitado
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
            //  Cargamos el listado correspondiente siempre que no sea el dashboard
            $this->controllerAction = "list";
        }

        // $this->controllerName = $value;
        return $this;

    }

    public function Run()
    {
        $this->setController( $_GET['route'] );
        // die('Acción: ' . $this->controllerAction);
        //  Comprobamos la seguridad
        $resultValidation = $this->checkSecurity();
        $userData = $resultValidation['data']->userData;

        if($resultValidation['status'] === true)
        {
            $this->userRol = $userData->role;
            $this->getController();
            $this->InitView();
            //die('Tiene acceso');
        }else{
            die('no tiene acceso');
        }

        //  Obtenemos el nombre del controlador que se va a utilizar

        //  Renderizamos la vista en función

    }

    public static function getUserAlias()
    {
        //  Recuperamos de la cookie el tipo de usuario que es para inicializar

        //  Si no hay cookie devolvemos un false para redireccionar

    }

    public static function redirectToLogin()
    {
        
    }



}