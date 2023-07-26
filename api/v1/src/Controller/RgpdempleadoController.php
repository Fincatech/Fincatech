<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\RgpdempleadoModel;

class RgpdempleadoController extends FrontController{

    private $rgpdEmpleadoModel;

    public function __construct($params = null)
    {
        $this->InitModel('Rgpdempleado', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->RgpdempleadoModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->RgpdempleadoModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->RgpdempleadoModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->RgpdempleadoModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->RgpdempleadoModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->RgpdempleadoModel->List($params);
    }

}