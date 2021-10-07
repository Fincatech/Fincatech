<?php

namespace Fincatech\Controller;


use Fincatech\Model\ContratosCesionModel;

class ContratosCesionController extends FrontController{

    private $contratosCesionModel;

    public function __construct($params = null)
    {
        $this->InitModel('ContratosCesion', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->ContratosCesionModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->ContratosCesionModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->ContratosCesionModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->ContratosCesionModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->ContratosCesionModel->Get($id);
    }

    public function List($params = null, $useLoggedUser = false)
    {
       return $this->ContratosCesionModel->List($params, $useLoggedUser);
    }

}