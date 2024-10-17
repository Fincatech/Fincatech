<?php

namespace  Fincatech\Controller;

use HappySoftware\Controller\HelperController;
use Fincatech\Model\UsuarioModel;
use Fincatech\Controller\ComunidadController;

use stdClass;

class UsuarioController extends FrontController{
    

    private $usuarioModel;
    public $UsuarioModel;

    private $usuario;

    protected $ComunidadController;

    public function __construct($params = null)
    {
        //$this->InitModel('Usuario', $params);
        $this->UsuarioModel = new UsuarioModel($params);
    }

    /**
     * Create user
     * @param string $entidadPrincipal. Entity Name
     * @param json $datos. JSON Object with values to create
     */
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
            $datos['email'] = trim($datos['emailcontacto']);
        }else{
            $datos['emailcontacto'] = trim($datos['email']);
        }

        //  Normalización IBAN
        if(isset($datos['ibanliquidaciones'])){
            $datos['ibanliquidaciones'] = HelperController::NormalizeIBAN($datos['ibanliquidaciones']);
        }

        $datos['email'] = trim($datos['email']);

        if($this->ExisteEmailLogin( $datos['email'] ))
        {
            return HelperController::errorResponse('error','El e-mail ya existe', 200);
        }else{
            //  Llenamos el modelo y lo guardamos
            // $this->UsuarioModel->Fill('usuario');
            // $this->UsuarioModel->_Save();
            //  Llamamos al método de crear
            return $this->UsuarioModel->Create($entidadPrincipal, $datos);
        }

        
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {

        //TODO: Hay que recuperar el usuario original para poder realizar la actualización de los datos que corresponda y que vengan informados

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

        //  Check if user status is H (Histórico) or B (Baja)
        if(isset($datos['estado']))
        {
            //  Check if user status is H (Histórico) or B (Baja)
            if($datos['estado'] == 'H' || $datos['estado'] == 'B')
            {
                $this->MoveCommunitiesToHistorico($usuarioId);
            }
        }

        //  Normalización IBAN
        if(isset($datos['ibanliquidaciones'])){
            $datos['ibanliquidaciones'] = HelperController::NormalizeIBAN($datos['ibanliquidaciones']);
        }

        return $this->UsuarioModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->UsuarioModel->getSchema();
    }

    public function Delete($id)
    {
        $this->MoveCommunitiesToHistorico($id);
        return $this->UsuarioModel->Delete($id);
    }

    public function Get($id)
    {

        $this->usuario = $this->UsuarioModel->Get($id);

        if(count($this->usuario) > 0)
        {
            //  Quitamos los campos de password
            unset($this->usuario['Usuario'][0]['password']);
        }

        return $this->usuario;
    }

    /**
     * Este método se utiliza para recuperar únicamente la información esencial y necesaria para la parte del front
     */
    public function GetResumedInfo()
    {
        
        $usuario = [];

        $usuarioId = $this->getLoggedUserId();
        // die('UsuarioId: [' . $usuarioId . ']');
        if($usuarioId > 0 ){
            $this->usuario = $this->UsuarioModel->Get($usuarioId);
        }else{
            $this->usuario = [];
        }

        if(count($this->usuario) > 0)
        {

            $usuarioData = $this->usuario['Usuario'][0];

            $rpgd = null;
            $cae = null;

            if(!is_null($usuarioData['idadministrador']))
            {
                $usuarioAdministrador = $this->UsuarioModel->Get($usuarioData['idadministrador']);
                $rpgd = $usuarioAdministrador['Usuario'][0]['mostrarrgpd'];
                $cae = $usuarioAdministrador['Usuario'][0]['mostrarcae'];
            }
            
            //  ID
            $usuario['id'] = $usuarioData['id'];
            //  Nombre
            $usuario['nombre'] = $usuarioData['nombre'];
            //  Email
            $usuario['email'] = $usuarioData['email'];
            //  Login
            $usuario['login'] = $usuarioData['email'];
            //  Admin autorizado
            $usuario['authorized'] = $usuarioData['idadministrador'] == '' ? -1 : 1;
            //  CAE
            $usuario['mostrarcae'] = boolval(is_null($cae) ? $usuarioData['mostrarcae'] : $cae);
            //  RGPD
            $usuario['mostrarrgpd'] = boolval(is_null($rpgd) ? $usuarioData['mostrarrgpd'] : $rpgd);
            //  ROL
            $usuario['role'] = 'ROLE_' . strtoupper($usuarioData['rol'][0]['alias']);
            //  RGPD Firmada
            if(isset($usuarioData['rgpd'])){
                $usuario['rgpd'] = $usuarioData['rgpd'];
            }else{
                $usuario['rgpd'] = null;
            }

        }

        return $usuario;
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
        if(!empty($usuario) && @isset($usuario['Usuario']) && count($usuario) > 0)
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

    /** Inserta en una cookie los accesos para cae y rgpd que tiene establecidos el usuario */
    public function CheckAccess()
    {

        $accesos = Array(
            'cae' => '',
            'rgpd' =>  ''
        );

        if($this->isLogged()) 
        {
            $userId = $this->getLoggedUserId();
            $usuario = $this->Get($userId);

            $accesos['cae'] = $usuario['Usuario'][0]['mostrarcae'];
            $accesos['rgpd'] = $usuario['Usuario'][0]['mostrarrgpd'];
            $accesos = json_encode($accesos);
            //  Eliminamos la cookie
            setcookie('FINCATECHACCESS', '', time() - 3600, '/');
            //  Actualizamos la cookie
            setcookie('FINCATECHACCESS', $accesos, 0, '/');
            //  Forzamos la actualización de la cookie
            $_COOKIE['FINCATECHACCESS'] = $accesos;
            return $accesos;
        }else{
            setcookie('FINCATECHACCESS', '', time() - 3600, '/');
            return $accesos;
        }
    }

    /**
     * Moves communities of administrador to historic
     * @param int $usuarioId Administrador ID
     */
    private function MoveCommunitiesToHistorico($usuarioId)
    {
        //  Move related communities to user to Historic
        $this->InitController('Comunidad');
        //  Update comunities to H (Historic) Status
        $this->ComunidadController->BulkChangeStatus('H', null, $usuarioId);         
    }

}