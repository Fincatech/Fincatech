<?php
namespace Fincatech\Controller;


use Fincatech\Model\ComunidadModel;

class ComunidadController extends FrontController{

    private $comunidadModel;

    public function __construct($params = null)
    {
        $this->InitModel('Comunidad', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->ComunidadModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->ComunidadModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->ComunidadModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->ComunidadModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->ComunidadModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->ComunidadModel->List($params);
    }

    public function GetTable($params)
    {
        return $this->ComunidadModel->GetTable($params);
    }

}