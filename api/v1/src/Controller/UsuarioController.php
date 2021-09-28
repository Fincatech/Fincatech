<?php

namespace  Fincatech\Controller;


use Fincatech\Model\UsuarioModel;

class UsuarioController extends FrontController{

    private $usuarioModel;

    public function __construct($params = null)
    {
        $this->InitModel('Usuario', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        $datos['salt'] = '';

        //  TODO: Implementarlo en el formulario de administrador
        if( $datos['password'] === '')
        {
            $datos['password'] = md5('123456');
        }else{
            $datos['password'] = md5( $datos['password'] );
        }

        // FIXME: Arreglar el e-mail de contacto ya que no se debe hacer aquí
        $datos['email'] = $datos['emailcontacto'];

        //  Llamamos al método de crear
        return $this->UsuarioModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        $datos['salt'] = '';

        //  TODO: Implementarlo en el formulario de administrador
        if( $datos['password'] === '')
        {
            $datos['password'] = md5('123456');
        }else{
            $datos['password'] = md5( $datos['password'] );
        }

        // FIXME: Arreglar el e-mail de contacto ya que no se debe hacer aquí
        $datos['email'] = $datos['emailcontacto'];

        return $this->UsuarioModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->UsuarioModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->UsuarioModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->UsuarioModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->UsuarioModel->List($params);
    }

    /** Devuelve la lista de administradores de fincas */
    public function ListAdministradoresFincas($params = null)
    {
        $datos = $this->UsuarioModel->ListByRolId(5);
        // $datos = $this->UsuarioModel->getEntityByField("Usuario", "rolId", 5);

        // $datos['total'] = count($datos);
        return $datos;
    }

}