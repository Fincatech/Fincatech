<?php

namespace Fincatech\Controller;


use Fincatech\Model\NotasInformativasModel;

class NotasInformativasController extends FrontController{

    private $notasInformativasModel;

    public function __construct($params = null)
    {
        $this->InitModel('NotasInformativas', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->NotasInformativasModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->NotasInformativasModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->NotasInformativasModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->NotasInformativasModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->NotasInformativasModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->NotasInformativasModel->List($params);
    }

}