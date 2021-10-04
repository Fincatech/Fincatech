<?php
namespace Fincatech\Controller;


use Fincatech\Model\ComunidadModel;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\Traits\SecurityTrait;

class ComunidadController extends FrontController{

    use SecurityTrait;

    private $comunidadModel;

    public function __construct($params = null)
    {
        $this->InitModel('Comunidad', $params);
    }

    public function Create($entidadPrincipal, $datos)
    {

        //  Llamamos al método de crear
        //  Si no está informado, cogemos el usuario autenticado en el sistema
        if(!isset($datos['usuarioId']))
            $datos['usuarioId'] = $this->getLoggedUserId();
        
        //  Si es el usuario sudo el que ha dado de alta la comunidad
        //  forzamos que se guarde en estado 'A' (Activada) para que lo apruebe el admin del sistema
        if($this->getLoggedUserRole() == 'ROLE_SUDO')
            $datos['estado'] = 'A';

        //  Creamos la comunidad y obtenemos el id del registro para procesar los posibles servicios contratados
        //  así como los precios 
        if(isset($datos['comunidadservicioscontratados']))
        {
                $datosServiciosContratados = $datos['comunidadservicioscontratados'];

            //  Quitamos los datos para evitar que de error al guardar
                unset($datos['comunidadservicioscontratados']);

        }

        $idComunidad = $this->ComunidadModel->Create($entidadPrincipal, $datos);

        //  Insertamos los servicios contratados por la comunidad
        for($x = 0; $x < count($datosServiciosContratados); $x++)
        {
            $idServicio = $datosServiciosContratados[$x]['idservicio'];
            $precio = $datosServiciosContratados[$x]['precio'];
            $precioComunidad = $datosServiciosContratados[$x]['preciocomunidad'];
            $contratado = $datosServiciosContratados[$x]['contratado'];
            $this->ComunidadModel->InsertServicioContratado($idComunidad['id'], $idServicio, $precio, $precioComunidad, $contratado);
        }

        return $idComunidad;

    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
       //  Creamos la comunidad y obtenemos el id del registro para procesar los posibles servicios contratados
        //  así como los precios 
        if(isset($datos['comunidadservicioscontratados']))
        {
            $datosServiciosContratados = $datos['comunidadservicioscontratados'];

            //  Quitamos los datos para evitar que de error al guardar
                unset($datos['comunidadservicioscontratados']);

            //  Actualizamos los servicios contratados por la comunidad
                for($x = 0; $x < count($datosServiciosContratados); $x++)
                {
                    $idServicioComunidad = $datosServiciosContratados[$x]['idserviciocomunidad'];
                    $precio = $datosServiciosContratados[$x]['precio'];
                    $precioComunidad = $datosServiciosContratados[$x]['preciocomunidad'];
                    $contratado = $datosServiciosContratados[$x]['contratado'];
                    $this->ComunidadModel->UpdateServicioContratado($datos['id'], $datosServiciosContratados[$x]['idservicio'], $idServicioComunidad, $precio, $precioComunidad, $contratado);
                }
        }

        return $this->ComunidadModel->Update($entidadPrincipal, $datos, $usuarioId); 

    }

    public function getSchemaEntity()
    {
        return $this->ComunidadModel->getSchema();
    }

    public function Delete($id)
    {
        return $this->ComunidadModel->Delete($id);
    }

    public function Get($id)
    {
        return $this->ComunidadModel->Get($id);
    }

    public function List($params = null)
    {
       return $this->ComunidadModel->List($params);
    }

    public function GetTable($params)
    {
        return $this->ComunidadModel->GetTable($params);
    }

    public function ListServiciosContratadosByComunidadId($id)
    {
        return helperController::successResponse( $this->ComunidadModel->ListServiciosContratadosByComunidadId($id) );
    }

    /** Devuelve el listado de empresas por id de empleado */
    public function ListComunidadesByAdministradorId($id)
    {
        $data = [];
        $data['ComunidadesAdministrador'] = $this->ComunidadModel->ListComunidadesByAdministradorId($id);
        $data['total'] = count($data);
        return HelperController::successResponse( $data );
    }

    /** Devuelve las empresas asociadas a una comunidad */
    public function getEmpresasByComunidadId($id)
    {
        $data = [];
        $data['empresascomunidad'] = $this->ComunidadModel->GetEmpresasByComunidadId($id);
        $data['total'] = count($data);
        return HelperController::successResponse( $data );
    
    }

    /** Asigna una empresa a una comunidad */
    public function asignarEmpresa($idcomunidad, $idempresa)
    {
        return HelperController::successResponse( $this->ComunidadModel->asignarEmpresa($idcomunidad, $idempresa) );
    }

}