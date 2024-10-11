<?php

/*
    $this->Model hay que cambiarlo por $this->NombreModel
*/

namespace Fincatech\Controller;

// Sustituir Model por el nombre del modelo real. Ej: UsuarioModel
use Fincatech\Model\EmpleadoModel;
use HappySoftware\Controller\HelperController;

class EmpleadoController extends FrontController{

    private $empleadoModel;
    public $EmpleadoModel;

    public function __construct($params = null)
    {
        $this->InitModel('Empleado', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {
            $informacionEmpresaAsociada = false;

        //  Extraemos la información de empleadoEmpresa
            if(isset($datos['empleadoempresa']))
            {
                $empleadoEmpresa = $datos['empleadoempresa'];
                $informacionEmpresaAsociada = true;
                unset($datos['empleadoempresa']);
            }
        
        //  Extremos el id de la comunidad si viene informado para
        //  crear la posterior relación entre comunidad e id de empleado
            $idComunidad = null;
            if(@isset($datos['Empleado']['idcomunidad']))
            {
                $datos = $datos['Empleado'];
                $idComunidad = $datos['idcomunidad'];
                $datos['estado'] = 'A';
                unset( $datos['idcomunidad'] );
            }

        //  Llamamos al método de crear
            $idEmpleado = $this->EmpleadoModel->Create($entidadPrincipal, $datos);

        //  Guardamos la relación entre el empleado y la empresa
            if($informacionEmpresaAsociada)
            {
                $empleadoEmpresa['idempleado'] = $idEmpleado['id'];
                $empleadoEmpresa['estado'] = $datos['estado'];
                $empleadoEmpresa['fechaalta'] = 'now()';
                $this->EmpleadoModel->Create('empleadoempresa', $empleadoEmpresa);
            }

        //  Si es un alta de empleado de comunidad, guardamos la relación entre ambos
            if( !is_null($idComunidad) )
            {
                $empleadoComunidad = [];
                $empleadoComunidad['idempleado'] = $idEmpleado['id'];
                $empleadoComunidad['idcomunidad'] = $idComunidad;
                $empleadoComunidad['estado'] = $datos['estado'];
                $empleadoComunidad['fechaalta'] = 'now()';
                $this->EmpleadoModel->Create('empleadocomunidad', $empleadoComunidad);
            }

            return $idEmpleado; //
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
        return $this->EmpleadoModel->Update($entidadPrincipal, $datos, $usuarioId); 
    }

    public function getSchemaEntity()
    {
        return $this->EmpleadoModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->EmpleadoModel->Delete($id);
    }

    public function Get($id)
    {
        $data = [];
        $data = $this->EmpleadoModel->Get($id);

        //  Comprobamos si tiene empresa asociada el empleado. Teóricamente sí que debe tener la relación pero
        //  hay que verificar porque puede ser un empleado de comunidad
        if(!isset( $data['Empleado'][0]['empleadoempresa'][0]['idempresa'] ))
        {
            $data['Empleado'][0]['idempresa'] = null; 
        }else{
            $data['Empleado'][0]['idempresa'] = $data['Empleado'][0]['empleadoempresa'][0]['idempresa'];
        }
        return $data;
    }

    public function List($params = null)
    {
        return $this->EmpleadoModel->List($params) ;
    }

    public function ListEmpleadosByComunidadId($idcomunidad)
    {
        
        $data = $this->EmpleadoModel->GetEmpleadosByComunidadId($idcomunidad);
    //    die();
        return HelperController::successResponse( $data );
    }


    /** Devuelve el listado de empleados por el id de la empresa */
    public function ListEmpleadosByEmpresaId($idEmpresa)
    {
        // FIXME: Incluir documentación PRL y sacarlo de aquí para meterlo en el modelo
        $data = [];
        $data['Empleados'] = $this->EmpleadoModel->GetEmpleadosByEmpresaId($idEmpresa);
        return HelperController::successResponse( $data );
    }

    public function ListEmpleadosByComunidadAndEmpresa($idComunidad, $idEmpresa)
    {
        $data = [];
        $data['Empleado'] = $this->EmpleadoModel->GetEmpleadosByComunidadAndEmpresa($idComunidad, $idEmpresa);
        return HelperController::successResponse( $data );
    }

    /** Devuelve el listado de empresas por id de empleado */
    public function ListEmpresasByEmpleadoId($id)
    {
        $data = [];
        $data['Empresasempleado'] = $this->EmpleadoModel->GetEmpresasByEmpleadoId($id);
        return HelperController::successResponse( $data );
    }

    public function ListDocumentacionCAEEmpleado($id)
    {
        $data = [];
        $data['documentacioncae'] = $this->EmpleadoModel->GetDocumentacionEmpleado($id);
        return HelperController::successResponse( $data );
    }

    public function AsignarComunidad($idEmpleado, $idComunidad)
    {

        $resultado = $this->EmpleadoModel->AsignarComunidad($idEmpleado, $idComunidad);

        if($resultado !== false)
        {
            return HelperController::successResponse( 'ok' );
        }else{
            return HelperController::errorResponse( 'error', 'El empleado ya está asignado a la comunidad', '200' );
        }
        
    }

    /**
     * Elimina un empleado de una comunidad
     * @param int idComunidad
     * @param int idEmpleado
     */
    public function DeleteRelacionEmpleadoComunidad($idComunidad, $idEmpleado)
    {
        $resultado = $this->EmpleadoModel->BajaComunidad($idComunidad,  $idEmpleado);
        return HelperController::successResponse( 'ok' );
    }

}