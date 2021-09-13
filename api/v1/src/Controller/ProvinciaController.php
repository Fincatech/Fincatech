<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\ProvinciaModel;

class ProvinciaController extends FrontController{

    private $provinciaModel;

    public function __construct($params = null)
    {
        $this->InitModel('Provincia', $params);
    }

    // public function Create($entidadPrincipal, $datos)
    // {
    //     //  Llamamos al método de crear
    //     return $this->ProvinciaModel->Create($entidadPrincipal, $datos);
    // }

    // public function Update($entidadPrincipal, $datos, $usuarioId)
    // {
    //     return $this->ProvinciaModel->Update($entidadPrincipal, $datos, $usuarioId); 
    // }

    // public function getSchemaEntity()
    // {
    //     return $this->ProvinciaModel->getSchema();
    // }

    // public function Delete($id)
    // {
    //     return $this->ProvinciaModel->Delete($id);
    // }

    public function Get($id)
    {
        return $this->ProvinciaModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->ProvinciaModel->List();
    }

}