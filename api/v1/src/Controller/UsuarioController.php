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
        //  TODO: Generamos su salt aleatorio para luego el login para aplicar otro método
        //  de encriptación más seguro
            $datos['salt'] = '';

        //  Si no viene informada la contraseña creamos una por defecto
            if(!isset($datos['password']))
            {
                $datos['password'] = md5('12345');
            }

        //  Llamamos al método de crear
        return $this->UsuarioModel->Create($entidadPrincipal, $datos);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
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