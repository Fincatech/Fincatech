<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\RequerimientodescargasModel;

class RequerimientodescargasController extends FrontController{

    private $nombreModel;

    public function __construct($params = null)
    {
        $this->InitModel('Requerimientodescargas', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->RequerimientodescargasModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->RequerimientodescargasModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->RequerimientodescargasModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->RequerimientodescargasModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->RequerimientodescargasModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->RequerimientodescargasModel->List($params);
    }

}