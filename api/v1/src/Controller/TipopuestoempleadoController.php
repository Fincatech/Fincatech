<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\TipopuestoempleadoModel;

class TipopuestoempleadoController extends FrontController{

    private $nombreModel;

    public function __construct($params = null)
    {
        $this->InitModel('Tipopuestoempleado', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->TipopuestoempleadoModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->TipopuestoempleadoModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->TipopuestoempleadoModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->TipopuestoempleadoModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->TipopuestoempleadoModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->TipopuestoempleadoModel->List($params);
    }

}