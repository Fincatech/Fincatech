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
        $sql = "select * from view_empleadosempresa where idempleado = " . $idEmpleado;

        //  TODO: Si tiene datos hay que recuperar el estado los documentos, para eso hay que instanciar el controller
        //  de documentos

        return $this->query($sql);



    }

    /** Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = true)
    {
        $data = [];
        //return $this->getAll('u', $params);
        $data = parent::List($params);
        // //  Empresa en las que trabaja el usuario
        for($x=0; $x < count($data['Empleado']); $x++)
        {
            $data['Empleado'][$x]['empresasempleado'] = $this->GetEmpresasByEmpleadoId($data['Empleado'][$x]['id']);
        }
        return $data;
    }

}