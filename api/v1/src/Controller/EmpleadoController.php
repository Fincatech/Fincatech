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
            
        //  Llamamos al método de crear
            $idEmpleado = $this->EmpleadoModel->Create($entidadPrincipal, $datos);

        //  Guardamos la relación entre el empleado y la empresa
            if($informacionEmpresaAsociada)
            {
            // die(1);
                $empleadoEmpresa['idempleado'] = $idEmpleado['id'];
                $empleadoEmpresa['estado'] = $datos['estado'];
                $empleadoEmpresa['fechaalta'] = 'now()';
                $this->EmpleadoModel->Create('empleadoempresa', $empleadoEmpresa);
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
        return $this->EmpleadoModel->Get($id);
    }

    public function List($params = null)
    {
        return $this->EmpleadoModel->List($params) ;
    }

    /** Devuelve el listado de empleados por el id de la empresa */
    public function ListEmpleadosByEmpresaId($idEmpresa)
    {
        $data = [];
        $data['Empleados'] = $this->EmpleadoModel->GetEmpleadosByEmpresaId($idEmpresa);
        return HelperController::successResponse( $data );
    }

    /** Devuelve el listado de empresas por id de empleado */
    public function ListEmpresasByEmpleadoId($id)
    {
        $data = [];
        $data['Empresasempleado'] = $this->EmpleadoModel->GetEmpresasByEmpleadoId($id);
        return HelperController::successResponse( $data );
    }

}