<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\DpdModel;

class DpdController extends FrontController{

    private $dpdModel;

    public function __construct($params = null)
    {
        $this->InitModel('Dpd', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->DpdModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->DpdModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->DpdModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->DpdModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->DpdModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->DpdModel->List($params);
    }

}