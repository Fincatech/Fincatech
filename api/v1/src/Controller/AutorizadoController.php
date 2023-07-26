<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\AutorizadoModel;

class AutorizadoController extends FrontController{

    private $autorizadoModel;
    public $UsuarioController, $ComunidadController;
    public $AutorizadoModel;

    public function __construct($params = null)
    {
        //  Inicializamos el modelo del controller
        $this->InitModel('Autorizado', $params);
        
        //  Inicializamos el controller de usuario
        $this->InitController('Usuario', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
        //  Si el e-mail está asignado a un usuario devolvemos el error
            if( $this->UsuarioController->ExisteEmailLogin( $datos['email'] ) )
            {
                $data = [];
                $data['error'] = 'El e-mail proporcionado ya está asignado a otro usuario';
                return $data;
            }

        //  El rol id por defecto es el del administrador de fincas ya que tiene acceso al mismo dashboard pero con limitaciones
            $datos['rolid'] = 5;
            $datos['estado'] = "A";
            $datos['emailcontacto'] = $datos['email'];

            $comunidadesAsignadas = null;
            if(isset($datos['comunidadesasignadas']))
            {
                $comunidadesAsignadas = $datos['comunidadesasignadas'];
                unset($datos['comunidadesasignadas']);
            }

        //  Llamamos al método de crear
        $usuarioId = $this->UsuarioController->Create('Usuario', $datos);

        //  Guardamos la información de las comunidades asignadas
        if(!is_null($comunidadesAsignadas))
        {
            for($iAsignacion = 0; $iAsignacion < count( $comunidadesAsignadas ); $iAsignacion++)
            {
                $insertar = $comunidadesAsignadas[$iAsignacion]['asignada'];
                if($insertar)
                {
                    $this->GuardarComunidadAsignada($usuarioId['id'], $comunidadesAsignadas[$iAsignacion]['idcomunidad'], $insertar);
                }
            }
        }

        return $usuarioId;

    }

    public function GuardarComunidadAsignada($idAutorizado, $idComunidad, $insertar = true)
    {
        if($insertar)
        {
            $this->AutorizadoModel->InsertarComunidadAsignada($idAutorizado, $idComunidad);
        }else{
            $this->AutorizadoModel->EliminarComunidadAsignada($idAutorizado, $idComunidad);
        }

    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        //  Almacenamos la relación de comunidades asignadas al usuario
        if(isset($datos['comunidadesasignadas']))
        {
            $comunidadesAsignadas = $datos['comunidadesasignadas'];
            for($iAsignacion = 0; $iAsignacion < count( $comunidadesAsignadas ); $iAsignacion++)
            {
                $this->GuardarComunidadAsignada($usuarioId, $comunidadesAsignadas[$iAsignacion]['idcomunidad'], ($comunidadesAsignadas[$iAsignacion]['asignada']));
            }
            unset($datos['comunidadesasignadas']);
        }

        return $this->UsuarioController->Update('Usuario', $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->AutorizadoModel->getSchema();
    }

    public function Delete($id)
    {
        //  Eliminamos la asociacion de las comunidades al usuario
        $this->AutorizadoModel->EliminarComunidadesAsignadasAutorizado($id);
        //  Eliminamos el usuario de la base de datos ya que no nos es relevante
        return $this->AutorizadoModel->DeleteAutorizado($id);
    }

    public function Get($id)
    {

        $autorizado = $this->UsuarioController->Get($id);

        if(!is_null($autorizado))
        {
            $autorizado['Autorizado'] = $autorizado['Usuario'];
            unset($autorizado['Usuario']);
            if($autorizado['Autorizado'][0]['idadministrador'] !== $this->getLoggedUserId())
            {
                $autorizado['error'] = '403';
            }
        }

        return $autorizado;
    }

    public function List($params = null)
    {

        $params['filterfield'] = 'idadministrador';
        $params['filteroperator'] = '=';
        $params['filtervalue'] = $this->getLoggedUserId();

        //  Recuperamos el listado de usuarios
        $usuariosAdministrador = $this->UsuarioController->List($params);

        return $usuariosAdministrador;
    }

    public function ListComunidades($idUsuarioAutorizado = null)
    {
        //  Recuperamos el listado de comunidades del administrador
        $this->InitController('Comunidad');
        $this->InitController('Usuario');
        $this->InitController('Autorizado');
        $comunidades = $this->ComunidadController->List(null, true);

        //  Por cada una de las comunidades comprobamos si el usuario autorizado tiene acceso
        if(!empty($comunidades) && is_array($comunidades) )
        {
            if(isset($comunidades['Comunidad']))
            {

                $comunidadesAsignadas = $this->AutorizadoModel->ComunidadesAsignadas($idUsuarioAutorizado);

                for($iCom = 0; $iCom < count($comunidades['Comunidad']); $iCom++)
                {
                    $comunidadId = $comunidades['Comunidad'][$iCom]['id'];
                    //  Comprobamos las comunidades que pueda tener asignado el usuario
                    $autorizada = false;
                    if(!is_null($idUsuarioAutorizado))
                    {
                        $autorizada = (array_search($comunidadId, array_column($comunidadesAsignadas, 'idcomunidad')) !== false);
                    }

                    $comunidades['Comunidad'][$iCom]['asignada'] = $autorizada;

                }

            }
        }
        return $comunidades;
    }

    public function ComunidadesAsignadas($idAutorizado)
    {
        return $this->AutorizadoModel->ComunidadesAsignadas($idAutorizado);
    }

}