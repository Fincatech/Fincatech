<?php

namespace Fincatech\Model;

use Fincatech\Entity\Empleado;
use \HappySoftware\Controller\HelperController;

class EmpleadoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Empleado';

    private $tablasSchema = array("empleado, usuarioRol");

    /**
     * @var \Fincatech\Entity\Empleado
     */
    public $empleado;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function Get($id)
    {
        $data = parent::Get($id);

        //  Recuperamos la documentación prl para el empleado
        $data['Empleado'][0]['documentacionprl'] = $this->GetDocumentacionEmpleado($data['Empleado'][0]['id']);
        return $data;
    }

    public function GetEmpleadosByComunidadId($idcomunidad)
    {
        
        $sql = 'select * from view_empleadosempresa where idcomunidad = ' . $idcomunidad;
        $sql .= ' UNION ALL ';
        $sql .= 'select * from view_empleadoscomunidad where idcomunidad = ' . $idcomunidad;
        

        $data = [];
        $data['Empleado'] = $this->query( $sql );
        for($x = 0; $x < count($data['Empleado']); $x++)
        {
            //  Recuperamos los documentos asociados a PRL de empleado
                $data['Empleado'][$x]['documentacionprl'] = $this->GetDocumentacionEmpleado($data['Empleado'][$x]['id']);
        } 
        return $data;
    }

    /** Devuelve los empleados asociados a una empresa */
    public function GetEmpleadosByEmpresaId($idEmpresa)
    {
        $sql = "select * from view_empleadosempresa where idempresa = " . $idEmpresa;
        return $this->query($sql);

        //  TODO: Si tiene datos hay que recuperar el estado los documentos, para eso hay que instanciar el controller
        //  de documentos
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
        $sql = "SELECT * FROM fincatech.view_documentosempleado where @IDEMPRESAREQUERIMIENTO:=" . $id; 
        return $this->query($sql);
    }

}