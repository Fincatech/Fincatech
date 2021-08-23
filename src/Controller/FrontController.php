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

    public function getController()
    {
        return $this->controllerName;
    }
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
            }else{
                $this->_id = null;
                $this->controllerAction = $controllerInfo[1];
            }
        }

        $this->controllerName = $value;
        return $this;

    }

    public function Run()
    {
        $this->setController( $_GET['route'] );
        
        //  Comprobamos la seguridad
        $resultValidation = $this->checkSecurity();
        $userData = $resultValidation['data']->userData;
        if($resultValidation['status'] === true)
        {
            $this->userRol = $userData->role;
            $this->getController();
            $this->InitView();
            //$this->renderView();
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