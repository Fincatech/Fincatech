<?php

namespace Fincatech\Controller;


use Fincatech\Model\CamarasSeguridadModel;

class CamarasSeguridadController extends FrontController{

    private $camarasSeguridadModel;

    public function __construct($params = null)
    {
        $this->InitModel('CamarasSeguridad', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->CamarasSeguridadModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->CamarasSeguridadModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->CamarasSeguridadModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->CamarasSeguridadModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->CamarasSeguridadModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->CamarasSeguridadModel->List($params);
    }

}