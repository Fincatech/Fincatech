<?php

namespace Fincatech\Controller;


use Fincatech\Model\NotasinformativasModel;

class NotasinformativasController extends FrontController{

    private $notasInformativasModel;

    public function __construct($params = null)
    {
        $this->InitModel('Notasinformativas', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->NotasinformativasModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->NotasinformativasModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->NotasinformativasModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->NotasinformativasModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->NotasinformativasModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->NotasinformativasModel->List($params);
    }

}