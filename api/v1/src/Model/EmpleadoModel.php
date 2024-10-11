<?php

namespace Fincatech\Model;

use Fincatech\Entity\Empleado;
use \HappySoftware\Controller\HelperController;
use HappySoftware\Database\DatabaseCore;

class EmpleadoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Empleado';

    private $tablasSchema = array("empleado, usuarioRol");

    /**
     * @var \Fincatech\Entity\Empleado
     */
    public $empleado;

    public function __construct($params = null)
    {
        parent::__construct();
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    /** Elimina un empleado y todas sus asociaciones */
    public function Delete($id, $entity = null)
    {
        $sql = "delete from empleadocomunidad where idempleado = $id";
        $this->getRepositorio()->queryRaw($sql);
        $sql = "delete from empleadoempresa where idempleado = $id";
        $this->getRepositorio()->queryRaw($sql);
        return parent::Delete($id);
    }

    public function Update($entidadPrincipal, $datos, $usuarioId)
    {
            $idEmpresaTemporal = null;
        //  Hay que comprobar si está asociado a una empresa
        //  Este valor viene infomado de: idempresa
            if(isset($datos['empleadoempresa']['idempresa']))
            {
                $idEmpresaTemporal = $datos['empleadoempresa']['idempresa'];
                //  Quitamos el valor del array
                    unset($datos['idempresa']);
                    unset($datos['empleadoempresa']);
            }

        //  Guardamos la entidad principal
            parent::Update($entidadPrincipal, $datos, $usuarioId);

            if(!is_null($idEmpresaTemporal))
            {
                //  Guardamos la relación entre el empleado y la empresa
                    $this->SaveRelationBetweenEmpleadoAndEmpresa( $usuarioId, $idEmpresaTemporal, $datos['estado']);
            }

    }

    public function Get($id)
    {
        $data = parent::Get($id);

        //  Recuperamos la documentación prl para el empleado
        $data['Empleado'][0]['documentacionprl'] = $this->GetDocumentacionEmpleado($data['Empleado'][0]['id']);
        return $data;
    }


    private function SaveRelationBetweenEmpleadoAndEmpresa($idempleado, $idempresa, $estado)
    {

        //  Comprobamos si existe ya el empleado en la tabla de relación
            if( $this->getRepositorio()->ExisteRegistro('empleadoempresa', 'idempleado = ' . $idempleado) )
            {
                //  Si existe, únicamente actualizamos el ID de la empresa
                    $this->UpdateRelationBetweenEmpleadoAndEmpresa($idempleado, $idempresa, $estado);
            }else{
                //  Si no existe, generamos la relación
                $empleadoEmpresa['idempresa'] = ($idempresa == '-1' ? 'null' : $idempresa);
                $empleadoEmpresa['idempleado'] = $idempleado;
                $empleadoEmpresa['estado'] = $estado;
                $empleadoEmpresa['fechaalta'] = 'now()';
                parent::Create('empleadoempresa', $empleadoEmpresa, $estado);            
            }



    }

    /** Actualiza la relación entre el empleado y la empresa */
    private function UpdateRelationBetweenEmpleadoAndEmpresa($idempleado, $idempresa, $estado)
    {
        //  Si se ha dado de baja lo reflejamos
            $fechaBaja = ($estado == 'B' ? ' fechabaja = now() , ' : '');
        
            $sql = "update empleadoempresa set $fechaBaja idempresa = " . $idempresa . " where idempleado = " . $idempleado;

            $this->getRepositorio()->queryRaw($sql);

    }

    public function GetEmpleadosByComunidadId($idcomunidad)
    {
        $data = [];
        
        $sql = 'select * from view_empleadosempresa where idcomunidad = ' . $idcomunidad;
        $sql .= ' UNION ALL ';
        $sql .= 'select * from view_empleadoscomunidad where idcomunidad = ' . $idcomunidad;

        $data['Empleado'] = $this->query( $sql );
        
        for($x = 0; $x < count($data['Empleado']); $x++)
        {
            //  Recuperamos los documentos asociados a PRL de empleado
                //$data['Empleados'][$x]['documentacionprl'] = $this->GetDocumentacionEmpleado($data['Empleado'][$x]['idempleado']);
                $data['Empleado'][$x]['documentacionprl'] = $this->GetDocumentacionEmpleado($data['Empleado'][$x]['idempleado']);
        } 

        return $data;
    }

    /** Devuelve los empleados asociados a una empresa */
    public function GetEmpleadosByEmpresaId($idEmpresa)
    {
        $sql = "select * from view_empleadosempresa where idempresa = " . $idEmpresa;
        $data = $this->query($sql);

        //  Si tiene datos hay que recuperar el estado los documentos, para eso hay que instanciar el controller
        //  de documentos
        //  Documentación del empleado
        for($x = 0; $x < count($data); $x++)
        {
            //  Recuperamos los documentos asociados a PRL de empleado
            $data[$x]['documentacionprl'] = $this->GetDocumentacionEmpleado($data[$x]['id']);
        }           

        return $data;

        // return $this->query($sql);
    }

    public function GetEmpleadosByComunidadAndEmpresa($idComunidad, $idEmpresa)
    {
        $sql = "SELECT * FROM `view_empleadoscomunidadempresa` where idempresa = $idEmpresa and idcomunidad = $idComunidad";
        $data = $this->query($sql);
        if(count($data))
        {
            for($x = 0; $x < count($data); $x++)
            {
                //  Recuperamos los documentos asociados a PRL de empleado
                $data[$x]['documentacionprl'] = $this->GetDocumentacionEmpleado($data[$x]['idempleado']);
            }   
        }
        return $data;
    }

    /** Devuelve las empresass asociadas a un empleado */
    public function GetEmpresasByEmpleadoId($idEmpleado)
    {
        $sql = 'select * from view_empleadosempresa where idempleado = ' . $idEmpleado . ' ' ;

        //  TODO: Si tiene datos hay que recuperar el estado los documentos, para eso hay que instanciar el controller
        //  de documentos

        return $this->query($sql);

    }

    /** Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = true)
    {

        $data = [];
        $data = parent::List($params);

        //  Empresa en las que trabaja el usuario
            for($x=0; $x < count($data['Empleado']); $x++)
                $data['Empleado'][$x]['empresasempleado'] = $this->GetEmpresasByEmpleadoId($data['Empleado'][$x]['id']);

        //  Documentación del empleado
            for($x = 0; $x < count($data['Empleado']); $x++)
            {
                //  Recuperamos los documentos asociados a PRL de empleado
                    $data['Empleado'][$x]['documentacionprl'] = $this->GetDocumentacionEmpleado($data['Empleado'][$x]['id']);
            }        

        return $data;
    }

    public function GetDocumentacionEmpleado($id)
    {
        $sql = "SELECT * FROM view_documentosempleado where @p1:=" . $id; 
        return $this->query($sql);
    }

    /** Asigna un empleado a una comunidad */
    public function AsignarComunidad($idEmpleado, $idComunidad)
    {
        //  Comprobamos si ya existe el empleado asignado a la comunidad
        if($this->getRepositorio()->ExisteRegistro('empleadocomunidad', " idempleado = $idEmpleado and idcomunidad = $idComunidad" ) )
        {
            return false;
        }else{
            //  Inserta el registro en base de dtos y devuelve ok
            $sql = "insert into empleadocomunidad(idempleado, idcomunidad, estado, fechaalta, created, usercreate) values(";
            $sql .= $idEmpleado . ", ";
            $sql .= $idComunidad . ", ";
            $sql .= "'A', ";
            $sql .= "now(), ";
            $sql .= "now(), ";
            $sql .= $this->getLoggedUserId() . ") ";

            $this->queryRaw($sql);
            return 'ok';
        }

    }

    /** Da de baja un empleado en una comunidad
     * @param int idComunidad
     * @param int idEmpleado
     */
    public function BajaComunidad($idComunidad,  $idEmpleado)
    {
        $idEmpleado = DatabaseCore::PrepareDBString($idEmpleado);
        $idComunidad = DatabaseCore::PrepareDBString($idComunidad);
        $sql = "delete from empleadocomunidad where idempleado = $idEmpleado and idcomunidad = $idComunidad";
        $this->getRepositorio()->queryRaw($sql);
    }

}