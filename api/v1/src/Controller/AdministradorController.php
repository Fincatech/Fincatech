<?php

namespace Fincatech\Controller;

use Fincatech\Model\UsuarioModel;
use Fincatech\Controller\UsuarioController;

class AdministradorController extends FrontController{

    private $usuarioModel;

    public function __construct($params = null)
    {
        $this->InitModel('Administrador', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Llamamos al método de crear
        //  Tenemos que generar un salt aleatorio y una pass por defecto
        
        //$datos['salt'] = md5(time());
        $datos['salt'] = '';

        //  TODO: Implementarlo en el formulario de administrador
        $datos['password'] = md5('12345');

        return $this->AdministradorModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->AdministradorModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->AdministradorModel->getSchema();
    }

    public function Delete($id)
    {
    
        return $this->AdministradorModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->AdministradorModel->Get($id);
    }

    /** Devuelve el listado de administradores */
    public function List($params = null)
    {
        $this->InitController('Usuario', $params);
        $this->InitModel('Usuario', $params);
        $datos = $this->UsuarioController->ListAdministradoresFincas($params);
        return $datos;
    }

}