<?php

namespace Fincatech\Controller;


use Fincatech\Model\FicherosComunesModel;

class FicherosComunesController extends FrontController{

    private $ficherosComunesModel;

    public function __construct($params = null)
    {
        $this->InitModel('FicherosComunes', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->FicherosComunesModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->FicherosComunesModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->FicherosComunesModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->FicherosComunesModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->FicherosComunesModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->FicherosComunesModel->List($params);
    }


    
}