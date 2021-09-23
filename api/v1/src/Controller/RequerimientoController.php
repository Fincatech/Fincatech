<?php

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\RequerimientoModel;

class RequerimientoController extends FrontController{

    private $requerimientoModel;

    public function __construct($params = null)
    {
        $this->InitModel('Requerimiento', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->RequerimientoModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->RequerimientoModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->RequerimientoModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->RequerimientoModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->RequerimientoModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->RequerimientoModel->List($params);
    }

    //  Recuperar requerimientos relativos a comunidad
    public function ListRequerimientosComunidad($comunidadId)
    {
        return $this->RequerimientoModel->ListRequerimientosComunidad($comunidadId);
    }


}