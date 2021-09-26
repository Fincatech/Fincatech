<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\Model;

class ExampleController extends FrontController{

    private $nombreModel;

    public function __construct($params = null)
    {
        $this->InitModel('Usuario', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->Model->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->Model->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->Model->getSchema();
    }

    public function Delete($id)
    {
        return $this->Model->Delete($id);
    }

    public function Get($id)
    {
        return $this->Model->Get($id);
    }

    public function List($params = null)
    {
       return $this->Model->List($params);
    }

}