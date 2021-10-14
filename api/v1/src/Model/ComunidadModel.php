<?php

namespace Fincatech\Model;

use Fincatech\Controller\EmpresaController;

use Fincatech\Entity\Comunidad;

use Fincatech\Model\Requerimiento;
use Fincatech\Model\ServiciosModel;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\Traits\SecurityTrait;

class ComunidadModel extends \HappySoftware\Model\Model{

    use SecurityTrait;

    private $entidad = 'Comunidad';

    private $tablasSchema = array("comunidad");

    /**
     * @var \Fincatech\Entity\Usuario\Comunidad
     */
    public $comunidad;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);
        
        //  Instanciamos el controller de tipos de servicio
        $this->InitController('Servicios');

    }

    /** Obtiene el listado de servicios contratados por una comunidad */
    private function GetListadoServiciosContratados($id)
    {
        $data = $this->GetServiciosContratados($id);
        
        //  Validamos que haya servicioscontratados para si no crear el mismo modelo
        if(count($data) == 0)
        {
                //  Recuperamos los precios base del producto
                $data = $this->ServiciosController->List(null)['Tiposservicios'];
            // print_r($data);
            // die();
        }  
            
        return $data;
    }

    /** Route endpoint: FIXME: Recupera los servicios contratados por una comunidad */
    public function ListServiciosContratadosByComunidadId($id)
    {
        //  Recuperaos la Comunidad por su id
            $data = $this->Get($id);
        
        //  Recuperamos los servicios contratados por la comunidad
            $data['Comunidad'][0]['servicioscontratados'] = $this->GetListadoServiciosContratados($id);

        //  Por cada uno de los servicios comprobamos si la comunidad lo tiene activo
            return $data;
    }

    //  Recuperamos el listado de las comunidades
    private function GetServiciosContratados($idcomunidad)
    {
        $sql = "select * from view_comunidadservicioscontratados where idcomunidad = $idcomunidad order by nombre asc";
        $dataServiciosContratados = $this->query($sql);
        return $dataServiciosContratados;
    }

    /** Recupera el listado de comunidades asociadas a un administrador */
    public function ListComunidadesByAdministradorId($id)
    {
        $sql = "select * from comunidad where usuarioid = $id ";

        if(!$this->isSudo())
            $sql = " and estado = 'A' ";

        $sql .= " order by nombre asc";
        $data = $this->query($sql);
        return $data;  
    }

    /** @Override del método principal para obtener la comunidad */
    public function Get($id)
    {
        $data = parent::Get($id);

        // if(count($data['Comunidad']) > 0)
        $data['Comunidad'][0]['comunidadservicioscontratados'] = $this->GetListadoServiciosContratados($data['Comunidad'][0]['id']);

        //  Cargamos los documentos de la comunidad
        //  Recuperamos los documentos asociados a una comunidad
        $data['Comunidad'][0]['documentacioncomunidad'] = $this->GetDocumentacionComunidad($data['Comunidad'][0]['id']);
        return $data;

    }

    /** @Override Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = true)
    {
        //  Si el usuario autenticado es un sudo, no acotamos
        if($this->isSudo())
            $useLoggedUserId = true;

        $data = parent::List($params, $useLoggedUserId);

        if(!$this->isSudo())
        {
            $data = $this->filterresults($data, 'Comunidad', 'estado', 'A');
        }

        if(count($data['Comunidad']) > 0)
        {
            for($x=0; $x < count($data['Comunidad']); $x++)
            {
                $data['Comunidad'][$x]['comunidadservicioscontratados'] = $this->GetListadoServiciosContratados($data['Comunidad'][$x]['id']);

                //  Recuperamos los documentos asociados a una comunidad
                //$sql = "SELECT * FROM fincatech.view_documentoscomunidad where @IDEMPRESAREQUERIMIENTO:=" . $data['Comunidad'][$x]['id']; 
                $data['Comunidad'][$x]['documentacioncomunidad'] = $this->GetDocumentacionComunidad($data['Comunidad'][$x]['id']);
                //  TODO: Comprobamos si la comunidad tiene alguna empresa de tipo Autónomo asignada
            }


        }
      

        //  Recuperamos los servicios contratados por la comunidad
            return $data;
    }

    /** Inserta un servicio contratado por una comunidad 
     *  TODO: Debería de hacerse directamente mediante el controller de servicios basándonos en objetos
    */
    public function InsertServicioContratado($idComunidad, $idServicio, $precio, $precioComunidad, $contratado)
    {

        $sql = "insert into comunidadservicioscontratados(idcomunidad, idservicio, precio, preciocomunidad, contratado, created) values (";
        $sql .= $idComunidad . ", ";
        $sql .= $idServicio . ", ";
        $sql .= $precio . ", ";
        $sql .= $precioComunidad . ", ";
        $sql .= $contratado . ", now() ) ";
        $this->getRepositorio()->queryRaw($sql);

    }

    /** Actualiza un servicio para una comunidad
     * TODO: Debería de hacerse directamente mediante el controller de servicios basándonos en objetos
     */
    public function UpdateServicioContratado($idComunidad, $idServicio, $idServicioComunidad, $precio, $precioComunidad, $contratado)
    {

        //  Comprobamos primero si existe el servicio ya que si no debe darse de alta
        if($this->ExisteServicioComunidad($idComunidad, $idServicio))
        {
            $sql = "update comunidadservicioscontratados set ";
            $sql .= "precio = $precio, ";
            $sql .= "preciocomunidad = $precioComunidad, ";
            $sql .= "contratado = $contratado ";
            $sql .= "where id = $idServicioComunidad ";

            $this->getRepositorio()->queryRaw($sql);
        }else{
            $this->InsertServicioContratado($idComunidad, $idServicio, $precio, $precioComunidad, $contratado);
        }
    }

    /** Comprueba si existe un servicio asociado a una comunidad */
    private function ExisteServicioComunidad($idComunidad, $idServicio)
    {
       return $this->getRepositorio()->ExisteRegistro('comunidadservicioscontratados', "idcomunidad = $idComunidad and idservicio = $idServicio");
    }

    /**  Comprueba si una comunidad tiene empleados o alguna empresa asociada */
    private function ComunidadTieneEmpleado($id)
    {

        $empleados = $this->getRepositorio()->selectCount('empleadocomunidad', 'idcomunidad', '=', $id);
        $empresas = $this->getRepositorio()->selectCount('comunidadempresa', 'idcomunidad', '=', $id);

        if($empleados > 0 || $empresas > 0)
        {
            return true;
        }else{
            return false;
        }

    }

    public function GetDocumentacionComunidad($id, $tieneTrabajadores = false)
    {

        $sql = "SELECT * FROM view_documentoscomunidad where @p1:=" . $id; 
        $datos['comtipo'] = $this->query($sql);

        if(!$this->ComunidadTieneEmpleado($id))
        {
            $datos = $this->filterResults($datos, 'comtipo', 'tiporequerimiento', 'COM')['comtipo'];
        }else{
            $datos = $datos['comtipo'];
        }

        return $datos;

    }

    public function GetEmpresasByComunidadId($id)
    {
        //  Instanciamos el controller de empresa
        $this->InitController('Empresa');

            $sql = "select * from view_empresascomunidad where idcomunidad = $id";
            $data = $this->query($sql);

            if($data)
            {
                for($x = 0; $x < count($data); $x++)
                {
                    //  Recuperamos los documentos asociados a CAE de empresa y su estado
                        $sql = "SELECT * FROM view_documentoscaeempresa where @p1:=" . $data[$x]['id']; 
                        $data[$x]['documentacioncae'] = $this->query($sql);
                }
            }


            // $data = $this->EmpresaController->List(null)['Empresa'];
        // // $datos, $entity, $key, $value    
        // //  Filtramos los resultados por id de comunidad            
            // $data = $this->filterResults($data, 'Empresa', 'idcomunidad', $id);
            return $data;
    }

    /** Asigna una empresa a una comunidad */
    public function asignarEmpresa($idComunidad, $idEmpresa)
    {
        $sql = "insert into comunidadempresa(idcomunidad, idempresa, activa, created, usercreate) values (";
        $sql .= $idComunidad . ", ";
        $sql .= $idEmpresa . ", 1,  now(), ";
        $sql .= $this->getLoggedUserId() . " ";
        $sql .= " ) ";

        $this->getRepositorio()->queryRaw($sql);  
        return 'ok';
    }

}