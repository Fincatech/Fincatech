<?php

namespace  Fincatech\Controller;


use Fincatech\Model\UsuarioModel;
use HappySoftware\Controller\HelperController;

class UsuarioController extends FrontController{

    private $usuarioModel;
    public $UsuarioModel;

    private $usuario;

    public function __construct($params = null)
    {
        $this->InitModel('Usuario', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        $datos['salt'] = '';

        //  TODO: Implementarlo en el formulario de administrador
        if( trim($datos['password']) === '')
        {
            $datos['password'] = md5('123456');
        }else{
            $datos['password'] = md5( $datos['password'] );
        }

        // FIXME: Arreglar el e-mail de contacto ya que no se debe hacer aquí
        //  19012023: Se comprueba el email contacto para evitar error
        if(isset($datos['emailcontacto'])){
            $datos['email'] = $datos['emailcontacto'];
        }else{
            $datos['emailcontacto'] = $datos['email'];
        }

        if($this->ExisteEmailLogin( $datos['email'] ))
        {
            return HelperController::errorResponse('error','El e-mail ya existe', 200);
        }else{
            //  Llamamos al método de crear
            return $this->UsuarioModel->Create($entidadPrincipal, $datos);
        }

        
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        
        if( isset($datos['password']) )
        {
            $datos['salt'] = '';

            //  Implementarlo en el formulario de administrador
            if( trim($datos['password']) === '')
            {
                unset($datos['password']);
            }else{
                $datos['password'] = md5( $datos['password'] );
            }
        }

        // FIXME: Arreglar el e-mail de contacto ya que no se debe hacer aquí
        if( isset($datos['email']) && isset($datos['emailcontacto']) )
        {
            // $datos['email'] = $datos['emailcontacto'];
        }

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
        $this->usuario = $this->UsuarioModel->Get($id);
        return $this->usuario;
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

    /**
     * Returns users with rol Técnico Certificados digitales
     */
    public function ListTecnicosRAO($params = null)
    {
        return $this->UsuarioModel->ListByRolId(8);
    }

    /** Recupera el ID del administrador al que pertenece */
    public function MainAdminId($userId = null)
    {
        //  No se pide el ID de usuario
        if($userId === null)
        {
            //  Recuperamos el ID sobre el usuario autenticado
            $usuarioId = $this->getLoggedUserId();
            $this->Get($usuarioId);
        }

        // Si no tenemos el usuario cargado
        if(is_null($this->usuario) && empty($this->usuario) )
            $this->Get( $this->getLoggedUserId() );

        if(!is_null($this->usuario) && !empty($this->usuario))        
        {
            return $this->usuario['Usuario'][0]['idadministrador'];
        }else{
            return false;
        }

    }

    /** Comprueba si es un usuario autorizado por un administrador */
    public function IsAuthorizedUserByAdmin($userId)
    {

        $resultado = false;

        //  Recuperamos el usuario
        $usuario = $this->Get($userId);

        //  Si el usuario existe, recuperamos el id del administrador                
        if(!empty($usuario) && @isset($usuario['Usuario']))
        {
            $administradorId = $usuario['Usuario'][0]['idadministrador'];
        }

        //  Si tiene usuario de administrador, devolvemos su ID
        if(!is_null($administradorId))
            $resultado = $administradorId;

        return $resultado;

    }

    /** Valida si existe un usuario por el e-mail proporcionado */
    public function ExisteEmailLogin($email)
    {
        return $this->UsuarioModel->ValidateUserEmail($email);
    }

}