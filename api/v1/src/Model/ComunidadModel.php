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

    public $DocumentalController, $ServiciosController;

    private $entidad = 'Comunidad';

    //  Se utiliza para comprobar si es un usuario autorizado
    public $administradorIdUsuarioAutorizado = -1;

    private $tablasSchema = array("comunidad");

    /**
     * @var \Fincatech\Entity\Usuario\Comunidad
     */
    public $comunidad;

    public function setAdministradorIdUsuarioAutorizado($value){
        $this->administradorIdUsuarioAutorizado = $value;
    }

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);
        
        // //  Instanciamos el controller de tipos de servicio
        // $this->InitController('Servicios');

    }

    /** Obtiene el listado de servicios contratados por una comunidad */
    private function GetListadoServiciosContratados($id)
    {
        if(is_null($id))
            return;

        $data = $this->ServiciosContratados($id);

        //  Validamos que haya servicioscontratados para si no crear el mismo modelo
        // if(count($data) == 0)
        if(is_null($data) || @count($data) == 0)
        {
            //  Recuperamos los precios base del producto
            $data = $this->ServiciosController->List(null)['Tiposservicios'];
        }  
        
        return $data;

    }

    /** Route endpoint: FIXME: Recupera los servicios contratados por una comunidad */
    // public function ListServiciosContratadosByComunidadId($id)
    // {
    //     //  Recuperamos la Comunidad por su id
    //         $data = $this->Get($id);
        
    //     //  Recuperamos los servicios contratados por la comunidad
    //         $data['Comunidad'][0]['servicioscontratados'] = $this->GetListadoServiciosContratados($id);

    //     //  Por cada uno de los servicios comprobamos si la comunidad lo tiene activo
    //         return $data;
    // }

    //  Recuperamos el listado de las comunidades
    public function ServiciosContratados($idcomunidad)
    {
        $sql = "select * from view_comunidadservicioscontratados where idcomunidad = $idcomunidad order by nombre asc";
        return $this->query($sql);
    }

    /** Recupera el listado de comunidades asociadas a un administrador */
    public function ListComunidadesByAdministradorId($id, $fechaDesde = null, $fechaHasta = null)
    {
        $sql = "select * from comunidad where usuarioId = $id ";

        if(!$this->isSudo())
        {
            $sql .= " and estado = 'A' ";
        }

        //  Si solo tiene fecha desde
        if(!empty($fechaDesde) && empty($fechaHasta))
        {
            $sql .= " and created >= '$fechaDesde' ";
        }

        //  Si tiene fecha desde y fecha hasta
        if(!empty($fechaDesde) && !empty($fechaHasta))
        {
            $sql .= " and created between ('$fechaDesde') and ('$fechaHasta')";
        }


        $sql .= " order by nombre asc";
        $data = $this->query($sql);
        return $data;  
    }

    /** @Override del método principal para obtener la comunidad */
    public function Get($id, $_extraInfo = true)
    {
        return parent::Get($id);
    }

    public function List($params = null, $useLoggedUserId = true)
    {
        return parent::List($params, $useLoggedUserId);
    }

    public function ListComunidadesMenuContratista($params = null)
    {
        $data = [];
        $idUsuarioEmpresa = $this->getLoggedUserId();
        $sql = "
        SELECT 
            c.*
        FROM
            comunidad c,
            comunidadempresa ce,
            empresa e
        WHERE
            e.idusuario = $idUsuarioEmpresa
            and ce.idempresa = e.id
            and c.id = ce.idcomunidad        
        ";
        $sql .= " order by cast(c.codigo as signed) asc";

        $result = $this->query($sql);
        $data['Comunidad'] = $result;
        return $data;        
    }

    /** Lista las comunidades en el menú lateral */
    public function ListComunidadesMenu($params = null, $useLoggedUserId = true, $administradorId = null)
    {
        $data = [];

        $search = '';

        if($this->isAdminFincas() || $useLoggedUserId){
            $search = ' where ';
        }

        if($this->isAdminFincas()){
            $search .= " estado in ('A','P') ";
        }

        $sql = "select * from comunidad $search";

        if($search != '' && $useLoggedUserId)
        {
            if( strpos( $search, 'estado') !== false)
            {
                $sql .= " and ";
            }
        }

        if($useLoggedUserId)
        {
            if(!is_null($administradorId))
            {
                $usuarioId = $administradorId;
            }else{
                $usuarioId = $this->getLoggedUserId();
            }
            $sql .= ' usuarioId = ' . $usuarioId . " ";
        }

        $sql .= " order by cast(codigo as signed) asc";

        $result = $this->query($sql);
        $data['Comunidad'] = $result;
        return $data;

    }

    public function ListServiciosContratadosComunidades($params){

        //  Parámetros de paginación
        $limiteConsulta = '';
        $limitStart = (isset($params['start']) ? $params['start'] : null);
        $limitLength = (isset($params['length']) ? $params['length'] : null);
        $searchText = (isset($params['search']) ? $params['search'] : null);
        $idAdministrador = (isset($params['administradorId']) ? $params['administradorId'] : null);
        //  Parámetros para la fecha de alta de la comunidad

        $totalComunidades = 0;
        
        //  Límite de la consulta en paginación
        if(!is_null($limitStart) && !is_null($limitLength)){
            $limiteConsulta =  " limit " . $limitStart . "," . $limitLength;
        }

        $administradorSearch = null;

        if(!is_null($idAdministrador))
        {
            $administradorSearch = " c.usuarioId = $idAdministrador ";

            //  Comprobamos si tiene fecha
            $fechaDesde = isset($params['fechaDesde']) ? $params['fechaDesde'] : null;
            $fechaHasta = isset($params['fechaHasta']) ? $params['fechaHasta'] : null;

            if(!empty($fechaDesde) && empty($fechaHasta))
            {
                $administradorSearch .= " and c.created >= '$fechaDesde' ";
            }

            //  Si tiene fecha desde y fecha hasta
            if(!empty($fechaDesde) && !empty($fechaHasta))
            {
                $administradorSearch .= " and c.created between ('$fechaDesde') and ('$fechaHasta')";
            }
        }

        //  Texto de búsqueda
        if( !is_null($searchText) && $searchText != '')
        {
            $searchText = " where (c.codigo like '%$searchText%' or c.nombre like '%$searchText%' or u.nombre like '%$searchText%') " ;
            if(!is_null($administradorSearch)){
                $searchText .= " and $administradorSearch ";
            }
        }else{
            if(!is_null($administradorSearch))
            {
                $searchText = " where $administradorSearch and c.estado = 'A' ";
            }
        }

        $sqlComunidades = "
            SELECT 
                c.id, cast(c.codigo as signed) codigo, c.cif, c.direccion, c.codpostal, c.provincia, c.nombre as comunidad, u.nombre as administrador, c.localidad, c.cif, c.ibancomunidad, c.created as fechaalta, c.estado
            FROM 
                comunidad c
                left join usuario u on u.id = c.usuarioid
                $searchText
            order by u.nombre asc, c.codigo asc
            $limiteConsulta";

        $sqlCount = "
            SELECT COUNT(*) as total from (
                SELECT 
                c.id, cast(c.codigo as signed) codigo, c.nombre as comunidad, u.nombre as administrador, c.localidad, c.cif, c.ibancomunidad, c.created as fechaalta, c.estado
            FROM 
                comunidad c
                left join usuario u on u.id = c.usuarioid
                $searchText
            ) t1
        ";

        $sqlResult = $this->query($sqlCount);
        $totalComunidades = $sqlResult[0]['total'];

        $comunidades = $this->query($sqlComunidades);

        if(count($comunidades))
        {
            $sqlTiposServicios = "select * from tiposservicios order by nombre asc";
            $servicios = $this->query($sqlTiposServicios);//GetListadoServicios();

            //  Por cada una de las comunidades comprobamos todos los servicios
            for($x = 0; $x < count($comunidades); $x++)
            {
                for($iServicio = 0; $iServicio < count($servicios); $iServicio++)
                {

                    //  Nombre Servicio
                    $nombreServicio = strtolower($servicios[$iServicio]['nombre']);
                    // $nombreServicio = str_replace(' ', '_', strtolower($servicios[$iServicio]['nombre']));
                    //  Comprobamos si lo tiene contratado así como los precios
                    $infoServicio = $this->InfoServicioContratadoComunidad($comunidades[$x]['id'], $servicios[$iServicio]['id']);
                    $comunidades[$x][$nombreServicio] = $infoServicio['id'];
                    $comunidades[$x][$nombreServicio.'_idtiposervicio'] = $servicios[$iServicio]['id'];
                    $comunidades[$x][$nombreServicio.'_contratado'] = $infoServicio['contratado'];
                    $comunidades[$x][$nombreServicio.'_precio'] = $infoServicio['precio'];
                    $comunidades[$x][$nombreServicio.'_preciocomunidad'] = $infoServicio['preciocomunidad'];
                    $comunidades[$x][$nombreServicio.'_retorno'] = $infoServicio['retorno'];
                    $comunidades[$x][$nombreServicio.'_mesfacturacion'] = $infoServicio['mesfacturacion'];
                    $comunidades[$x][$nombreServicio.'_fechaalta'] = $infoServicio['fechaalta'];
                    
                }
            }
        }
        $data = [];
        $data['Comunidad'] = $comunidades;
        $data['total'] = $totalComunidades;
        return $data;
    }

    /** Devuelve la información relativa a un servicio por comunidad si éste ha sido contratado */
    private function InfoServicioContratadoComunidad($idComunidad, $idServicio)
    {
        $info = [];
        $info['id'] = 0;
        $info['contratado'] = false;
        $info['precio'] = 0;
        $info['preciocomunidad'] = 0;
        $info['retorno'] = 0;
        $info['fechaalta'] = 'N/D';
        $info['mesfacturacion'] = '12';

        $sqlServicioContratado = "select * from comunidadservicioscontratados where idservicio = $idServicio and idcomunidad = $idComunidad";
        $servicioComunidad = $this->query($sqlServicioContratado);

        if(count($servicioComunidad))
        {
            $info['id'] = $servicioComunidad[0]['id'];
            $info['contratado'] = ($servicioComunidad[0]['contratado'] == 1 ? true : false);
            $info['precio'] = $servicioComunidad[0]['precio'];
            $info['preciocomunidad'] = $servicioComunidad[0]['preciocomunidad'];
            $info['retorno'] = $servicioComunidad[0]['retorno'];
            $info['fechaalta'] = $servicioComunidad[0]['created'];
            $info['mesfacturacion'] = $servicioComunidad[0]['mesfacturacion'];
        }

        return $info;
    }

    /** Inserta un servicio contratado por una comunidad 
     *  TODO: Debería de hacerse directamente mediante el controller de servicios basándonos en objetos
    */
    public function InsertServicioContratado($idComunidad, $idServicio, $precio, $precioComunidad, $contratado, $mesFacturacion = 1)
    {

        $sql = "insert into comunidadservicioscontratados(idcomunidad, idservicio, precio, preciocomunidad, contratado, mesfacturacion, created) values (";
        $sql .= $idComunidad . ", ";
        $sql .= $idServicio . ", ";
        $sql .= $precio . ", ";
        $sql .= $precioComunidad . ", ";
        $sql .= $contratado . ", ";
        $sql .= $mesFacturacion . ", now() ) ";
        $this->getRepositorio()->queryRaw($sql);

    }

    /** Actualiza un servicio para una comunidad
     * TODO: Debería de hacerse directamente mediante el controller de servicios basándonos en objetos
     */
    public function UpdateServicioContratado($idComunidad, $idServicio, $idServicioComunidad, $precio, $precioComunidad, $contratado, $mesFacturacion)
    {

        //  Comprobamos primero si existe el servicio ya que si no debe darse de alta
        if($this->ExisteServicioComunidad($idComunidad, $idServicio))
        {
            $sql = "update comunidadservicioscontratados set ";
            $sql .= "precio = $precio, ";
            $sql .= "preciocomunidad = $precioComunidad, ";
            $sql .= "contratado = $contratado, ";
            $sql .= "mesfacturacion = $mesFacturacion ";
            $sql .= "where id = $idServicioComunidad ";

            $this->getRepositorio()->queryRaw($sql);
        }else{
            $this->InsertServicioContratado($idComunidad, $idServicio, $precio, $precioComunidad, $contratado, $mesFacturacion);
        }
    }

    /** Comprueba si existe un servicio asociado a una comunidad */
    public function ExisteServicioComunidad($idComunidad, $idServicio)
    {
       return $this->getRepositorio()->ExisteRegistro('comunidadservicioscontratados', " idcomunidad = $idComunidad and idservicio = $idServicio ");
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

    /**
     * Recupera los documentos relativos al certificado digital
     */
    public function GetDocumentacionComunidadCertificadoDigital($id)
    {
        $sql = "SELECT * FROM view_certificadocomunidad where @p1:=" . $id; 
        return $this->query($sql);
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

    /** Recupera las empresas asignadas a la comunidad */
    public function GetEmpresasByComunidadId($id, $_getDocumentacionCae = true)
    {
        $sql = "select * from view_empresascomunidad where idcomunidad = $id group by id";
        $data = $this->query($sql);

        if($data && $_getDocumentacionCae === true)
        {
            for($x = 0; $x < count($data); $x++)
            {
                //  Recuperamos los documentos asociados a CAE de empresa y su estado
                    $sql = "SELECT * FROM view_documentoscaeempresa where @p1:=" . $data[$x]['idusuario']; 
                    $data[$x]['documentacioncae'] = $this->query($sql);
            }
        }
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

    /** Obtiene el registro de descargas para un fichero */
    public function GetDescargasByFicheroId($idFichero)
    {
        $sql = "select idfichero, idcomunidad, idempresa, idusuario, fechadescarga, fechaultimadescarga from ficherosdescargas where idfichero = $idFichero";
        return $this->query($sql);
    }

    /** Comprueba si una comunidad tiene un servicio determinado contratado */
    public function ServicioContratado($idComunidad, $idServicio)
    {
        return $this->getRepositorio()->getValue('comunidadservicioscontratados', 'contratado', "1  and idcomunidad= $idComunidad and idservicio = $idServicio ");
    }

    /** Calcula el grado de cumplimiento en materia documental según el tipo de servicio y por id de comunidad */
    private function GradoCumplimientoTipoServicio($idTipoRequerimiento, $idComunidad)
    {

        $this->InitController('Documental');

        $idAdministrador = $this->getLoggedUserId();

        $totalRequerimientos = 0;
        $totalRequerimientosPendientes = 0;
        $totalGradoCumplimiento = 0;
        $tieneEmpresasAsociadas = false;

        switch($idTipoRequerimiento)
        {
            case 1:     //  CAE
                
                //  Recuperamos el número de requerimientos obligatorios de CAE
                    $totalRequerimientos = (int)$this->getRepositorio()->selectCount('view_documentoscomunidad');

                    $empresasAsociadas = $this->GetEmpresasByComunidadId($idComunidad);
                    $tieneEmpresasAsociadas = (count($empresasAsociadas) > 0 ? true : false);

                    if($tieneEmpresasAsociadas)
                    {
                        $totalRequerimientos++;
                    }

                //  Recuperamos el número de requerimientos obligatorios de CAE que tiene adjuntados
                    $requerimientosPendientes = $this->DocumentalController->DocumentalModel->GetRequerimientosPendientesCAE($this->getLoggedUserId(), $idComunidad, $tieneEmpresasAsociadas);
                    if(is_array($requerimientosPendientes))
                    {
                        $totalRequerimientosPendientes = count($requerimientosPendientes);
                    }else{
                        $totalRequerimientosPendientes = 0;
                    }

                    if(is_array($empresasAsociadas))
                    {

                    //  Comprobar documentos pendientes de empresa externa
                        if(count($empresasAsociadas) > 0)
                        {
                        //     //  Recuperamos las empresas asociadas a la comunidad
                        //     //  Por cada una de ellas comprobamos el cumplimiento de los requerimientos que ya viene informado
                            for($iEmpresa = 0; $iEmpresa < count($empresasAsociadas); $iEmpresa++)
                            {
                                //  TODO: Comprobamos además si tiene el contrato de declaración responsable entre empresa y administrador
                                if(is_array($empresasAsociadas[$iEmpresa]['documentacioncae']))
                                {
                                    //  Recorremos todos los documentos del cae por cada empresa para comprobar cuántos le faltan
                                    for($iDocCae = 0; $iDocCae < count($empresasAsociadas[$iEmpresa]['documentacioncae']); $iDocCae++ )
                                    {
                                        $totalRequerimientos++;

                                        //  Si no viene informado el fichero es que no está completado
                                        $idFicheroRequerimiento = $empresasAsociadas[$iEmpresa]['documentacioncae'][$iDocCae]['idficherorequerimiento'];
                                        $estadoFichero = $empresasAsociadas[$iEmpresa]['documentacioncae'][$iDocCae]['idestado'];

                                        if( is_null( $idFicheroRequerimiento ) )
                                        {
                                            $totalRequerimientosPendientes++;
                                        }else{
                                            // Si viene informado debemos comprobar el estado en el que se encuentra
                                            if($estadoFichero == 1 || $estadoFichero == 3 || $estadoFichero == 7)
                                            {
                                                $totalRequerimientosPendientes++;
                                            }
                                        }

                                    }
                                }
                            }
                        }
                    }
                    unset($empresasAsociadas);
                    $totalGradoCumplimiento = round( (( ( ($totalRequerimientos - $totalRequerimientosPendientes ) * 100) / $totalRequerimientos)), 2);

                break;
            case 2:     //  RGPD
                
                    $infoComunidad = $this->Get($idComunidad, false);
                    $tieneCamaraSeguridad = $infoComunidad['Comunidad'][0]['camarasseguridad'];

                //  Recuperamos el total de requerimientos de RGPD para una comunidad
                    $totalRequerimientos            = $this->DocumentalController->GetTotalRequerimientosRGPDComunidad( $idAdministrador, $idComunidad, $tieneCamaraSeguridad );  
                //  Calculamos el número de requerimientos pendientes de RGPD para una comunidad
                    $totalRequerimientosPendientes  = $this->DocumentalController->GetTotalRequerimientosPendientesRGPDComunidad($idComunidad, $idAdministrador, $tieneCamaraSeguridad);

                    if($totalRequerimientosPendientes > 0)
                    {
                        $totalGradoCumplimiento = round( ((($totalRequerimientos - $totalRequerimientosPendientes) * 100) / $totalRequerimientos), 2);
                    }else{
                        $totalGradoCumplimiento = 100;
                    }
                break;
        }

        return ($totalGradoCumplimiento >= 100 ? 100 : $totalGradoCumplimiento); 

    }

    /**
     * Elimina la relación entre empresa y comunidad
     */
    public function DeleteRelacionEmpresaComunidad($idComunidad, $idEmpresa)
    {
        //  Eliminamos la asociación de la empresa con la comunidad
            $result = $this->getRepositorio()->delete('comunidadempresa', $idComunidad . " and idempresa = " . $idEmpresa, DELETE_FISICO, false, "idcomunidad");

        //  FIX: Se está guardando el id de usuario para la empresa en vez del id real de la empresa
        //  Recuperamos el ID de usuario en base al ID de la empresa
            $idEmpresa = $this->getRepositorio()->getValue('empresa', 'idusuario', $idEmpresa, 'id');

        //  Eliminamos los empleados de la empresa que se quita la asociación
            $sql = "delete from empleadocomunidad
                    where idempleado in(SELECT idempleado FROM empleadoempresa where idempresa = $idEmpresa) and idcomunidad = $idComunidad";
            $this->getRepositorio()->queryRaw($sql);

        return $result;
    }

    /** Comprueba si se ha enviado el e-mail certificado a una empresa para una comunidad */
    public function EmailCertificadoEnviadoEmpresa($idComunidad, $idEmpresa)
    {
        //  Select count
        $sql = "select * from emailscertificados where idcomunidad = $idComunidad and idempresa = $idEmpresa and filename is not null order by id desc limit 1";
        return $this->query($sql);
    }

}