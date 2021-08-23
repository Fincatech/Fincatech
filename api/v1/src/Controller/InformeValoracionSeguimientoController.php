<?php

namespace Fincatech\Controller;


use Fincatech\Model\InformeValoracionSeguimientoModel;

class InformeValoracionSeguimientoController extends FrontController{

    private $informeValoracionSeguimientoModel;

    public function __construct($params = null)
    {
        $this->InitModel('InformeValoracionSeguimiento', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        return $this->InformeValoracionSeguimientoModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->InformeValoracionSeguimientoModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->InformeValoracionSeguimientoModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->InformeValoracionSeguimientoModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->InformeValoracionSeguimientoModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->InformeValoracionSeguimientoModel->List($params);
    }

}