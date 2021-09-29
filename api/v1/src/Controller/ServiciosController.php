<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\ServiciosModel;
use HappySoftware\Controller\HelperController;

class ServiciosController extends FrontController{

    private $serviciosModel;

    public function __construct($params = null)
    {
        $this->InitModel('Servicios', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->ServiciosModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->ServiciosModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->ServiciosModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->ServiciosModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->ServiciosModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->ServiciosModel->List($params);
    }

}