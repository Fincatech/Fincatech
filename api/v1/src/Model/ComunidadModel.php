<?php

namespace Fincatech\Model;

use Fincatech\Entity\Comunidad;

use Fincatech\Model\Requerimiento;

use Fincatech\Model\ServiciosModel;
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

        // Por cada uno de los servicios comprobamos si la comunidad lo tiene activo
        return $data;
    }

    //  Recuperamos el listado de las comunidades
    public function GetServiciosContratados($idcomunidad)
    {
        $sql = "select * from view_comunidadservicioscontratados where idcomunidad = $idcomunidad order by nombre asc";
        $dataServiciosContratados = $this->query($sql);
        return $dataServiciosContratados;
    }

    /** Recupera el listado de comunidades asociadas a un administrador */
    public function ListComunidadesByAdministradorId($id)
    {
        $sql = "select * from comunidad where usuarioid = $id order by nombre asc";
        $data = $this->query($sql);
        return $data;  
    }

    /** @Override del método principal para obtener la comunidad */
    public function Get($id)
    {
        $data = parent::Get($id);

        if(@count($data['Comunidad']) > 0)
            $data['Comunidad'][0]['comunidadservicioscontratados'] = $this->GetListadoServiciosContratados($data['Comunidad'][0]['id']);

        //  Cargamos los documentos de la comunidad

        return $data;

    }

    /** @Override Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = true)
    {
        //  Si el usuario autenticado es un sudo, no acotamos
        if($this->isSudo())
            $useLoggedUserId = true;

        $data = parent::List($params, $useLoggedUserId);
        if(count($data['Comunidad']) > 0)
        {
            for($x=0; $x < count($data['Comunidad']); $x++)
            {
                $data['Comunidad'][$x]['comunidadservicioscontratados'] = $this->GetListadoServiciosContratados($data['Comunidad'][$x]['id']);
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

}