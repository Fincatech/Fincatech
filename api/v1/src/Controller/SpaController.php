<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\SpaModel;

class SpaController extends FrontController{

    private $spaModel;

    public function __construct($params = null)
    {
        $this->InitModel('Spa', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->SpaModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->SpaModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->SpaModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->SpaModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->SpaModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->SpaModel->List($params);
    }

}