<?php

namespace Fincatech\Model;

use Fincatech\Entity\Documental;
use HappySoftware\Controller\HelperController;
use HappySoftware\Controller\HistoricoController;

class DocumentalModel extends \HappySoftware\Model\Model{

    public $HistoricoController;

    private $entidad = 'Requerimiento';

    private $tablasSchema = array("usuario", "usuarioRol");

    private $idFichero;
    private $idComunidad;
    private $idUsuario;
    private $idEmpresa;
    private $idDescargaFichero;

    /**
     * @var \Fincatech\Entity\Requerimiento
     */
    public $documental;

    public function setIdFichero($value){
        $this->idFichero = $value;
        return $this;
    }

    public function getIdFichero(){
        return $this->idFichero;
    }

    public function setIdComunidad($value){
        $this->idComunidad = $value;
        return $this;
    }
    public function getIdComunidad(){
        return $this->idComunidad;
    }

    public function setIdUsuario($value){
        $this->idUsuario = $value;
        return $this;
    }
    public function getIdUsuario(){
        return $this->idUsuario;
    }

    public function setIdEmpresa($value){
        $this->idEmpresa = $value;
        return $this;
    }
    public function getIdEmpresa(){
        return $this->idEmpresa;
    }

    public function setIdFicheroDescarga($value){
        $this->idDescargaFichero = $value;
        return $this;
    }
    public function getIdFicheroDescarga(){
        return $this->idDescargaFichero;
    }

    public function __construct($params = null)
    {
        parent::__construct();
        //  Inicializamos la entidad
        $this->InitEntity('Requerimiento');

        //  Inicializamos el modelo
        $this->InitModel('Requerimiento', $params, $this->tablasSchema);

    }

    public function getRelacionesTabla()
    {
        return [
            'comunidad' => [
                'tabla' => 'comunidadrequerimiento',
                'campo' => 'idcomunidad'
            ],
            'empleado'  => [
                'tabla' => 'empleadorequerimiento',
                'campo' => 'idempleado'
            ],
            'empresa'   => [
                'tabla' => 'empresarequerimiento',
                'campo' => 'idempresa'
            ],
            'certificado' => [
                'tabla' => 'certificadorequerimiento',
                'campo' => 'idcomunidad'
            ]
        ];
    }

    /** Asigna un nuevo documento al tipo seleccionado */
    public function createRequerimiento($idFichero, $datos)
    {
    
            $tablasRelaciones = $this->getRelacionesTabla();
            $destino = $datos['entidad'];

            $idComunidad = (isset($datos['idcomunidad']) ? $datos['idcomunidad'] : null);
            $idAdministrador = (isset($datos['idadministrador']) ? $datos['idadministrador'] : null);
            $idRequerimiento = $datos['idrequerimiento'];

            $comunidad = (isset($datos['idcomunidad']) ? ' idcomunidad, idestado, ' : '');

            if(  $tablasRelaciones[$destino]['tabla'] == 'comunidadrequerimiento'){
                return $this->createRequerimientoComunidad($idComunidad , $idRequerimiento, $idFichero, 4, $idAdministrador);
            }

        //  Generamos el registro en la base de datos
            $sql = "insert into " . $tablasRelaciones[$destino]['tabla'] . "($comunidad idrequerimiento, idfichero, " . $tablasRelaciones[$destino]['campo'] .", created) ";
            $sql .=" values (";

            $idEstado = '4'; // Adjuntado por defecto (Estado pendiente)

            if($tablasRelaciones[$destino]['tabla'] == 'empresarequerimiento' || 
                $tablasRelaciones[$destino]['tabla'] == 'empleadorequerimiento') {
                $idEstado = 3;
            }

        //  ID Comunidad (Si está enviado en el payload)
            if(!is_null($comunidad))
            {
                //  Estado: 4 ( Adjuntado )
                $sql .= $datos['idcomunidad'] . ", $idEstado, ";
            }

        //  ID requerimiento
            $sql .= $datos['idrequerimiento'] . ", " ;

        //  ID Fichero
            $sql .= $idFichero .", ";

        //  ID Campo Destino
            $sql .= $datos[$tablasRelaciones[$destino]['campo']] . ", ";

        //  Created
            $sql .= "now() ";

        //  Cierre de consulta
            $sql .= " ) ";

            $this->getRepositorio()->queryRaw( $sql );

        //  Devolvemos un estado ok
            return 'ok';

    }

    private function createRequerimientoCertificadoDigital()
    {
        $sql = "
        INSERT INTO certificadorequerimiento(`idusuario`,`idcomunidad`,`idrequerimiento`,`fechasubida`,
            `idfichero`,`idestado`,`observaciones`,`created`)
        VALUES ";
            
        //  ID de usuario
        $sql = $this->getLoggedUserId() . ', ';
        //  Id de comunidad

        //  Id Requerimiento

        //  Fecha de subida
        $sql .= 'now(),  ';
        //  ID fichero

        //  Id Estado

        //  Estado

        //  Observaciones

        //  Created
        $sql .= 'now() ) ';
        $this->getRepositorio()->queryRaw( $sql );
        return 'ok';
    }

    /** Refleja en el sistema el requerimiento para una comunidad */
    public function createRequerimientoComunidad($idcomunidad, $idrequerimiento, $idfichero, $idestado, $idadministrador = null)
    {
        if(is_null($idadministrador))
        {
            $idadministrador = 'null';
        }

        $sql = "insert into comunidadrequerimiento(idcomunidad, idrequerimiento, idfichero, idadministrador, estado, idestado, created) values (";
        $sql .= "$idcomunidad, $idrequerimiento, $idfichero, $idadministrador, $idestado, $idestado, now())";
        $this->getRepositorio()->queryRaw($sql);
        return 'ok';
    }

    //  Genera un requerimiento en base de datos
    public function createRequerimientoRGPD($destino, $idFichero, $datos)
    {

        $tablaDestino = '';
        $usercreate = '';
        $usercreateId='';

        switch (strtolower($destino))
        {
            case 'camarasseguridad':
                $tablaDestino = 'camarasseguridad';
                break;
            case 'contratoscesion':
                $tablaDestino = 'contratoscesion';
                break;
            case 'rgpdempleado':
                $tablaDestino = 'rgpdempleado';
                $datos['idcomunidad'] = 'null';
                $usercreate = "usercreate, ";
                $usercreateId = $this->getLoggedUserId() . ', ';
                break;
            default:
                return 'ok';
                break;
        }

        //  Comprobamos si existe el requerimiento

        if($datos['idrequerimiento'] == '-1')
        {
        //  Generamos el registro en la base de datos
            $sql = "insert into " . $tablaDestino . "(titulo, descripcion, idfichero, idcomunidad, $usercreate created) ";
            $sql .=" values (";

        //  Título
            $sql .= "'" . $this->getRepositorio()::PrepareDBString( $datos['titulo'] ) . "', ";

        //  Descripción
            $sql .= "'" . $this->getRepositorio()::PrepareDBString( $datos['observaciones'] ) . "', ";

        //  ID Fichero
            $sql .= $idFichero . ", ";

        //  ID Comunidad
            $sql .= $datos['idcomunidad'] . ", ";

        //  Created
            $sql .= " $usercreateId now() ";

        //  Cierre de consulta
            $sql .= " ) ";

            $this->getRepositorio()->queryRaw( $sql );

        }else{
            $this->updateRequerimiento($datos['idrequerimiento'], $idFichero, $tablaDestino);
        }

        //  Devolvemos un estado ok
            return 'ok';

    }

    //  Elimina un requerimiento de la base de datos
    public function DeleteRequerimiento($tiporequerimiento, $id)
    {
        $this->getRepositorio()->deleteSingle($tiporequerimiento, $id, false);
        return 'ok';
    }

    /** Comprueba si el requerimiento de cámaras de esguridad está insertado en el sistema */
    public function compruebaRequerimientoCamara($idComunidad)
    {
        return $this->getRepositorio()->ExisteRegistro('camarasseguridad', " idcomunidad=$idComunidad");
    }

    /** Cambia el estado a un requerimiento */
    public function cambiarEstadoRequerimiento($idRequerimiento, $entidadDestino, $estado, $fechaCaducidad, $observaciones)
    {
        $observaciones = $this->getRepositorio()::PrepareDBString($observaciones);
        $fechaCaducidad = $this->getRepositorio()::PrepareDBString($fechaCaducidad);
        $estado = $this->getRepositorio()::PrepareDBString($estado);
        $idRequerimiento = $this->getRepositorio()::PrepareDBString($idRequerimiento);
        $entidadDestino = $this->getRepositorio()::PrepareDBString($entidadDestino);

        $sql = "update $entidadDestino set idestado = $estado";
        if($fechaCaducidad != '')
        {
            $sql .= ", fechacaducidad='$fechaCaducidad' ";
        }
        $sql .= ", observaciones='$observaciones' where id = $idRequerimiento";
        $this->getRepositorio()->queryRaw($sql);
        
    }

    /**
     * Actualiza la información de un requerimiento
     */
    public function updateRequerimiento($idRequerimiento, $idFicheroNuevo, $tabla, $fechaCaducidad = null)
    {

            $estado = '';
            //  Si es un requerimiento de empleado, empresa o certificado digital se actualiza al estado pendiente
            //  ya que es un técnico el que debe validar el requerimiento
            if($tabla == 'empleadorequerimiento' || $tabla == 'empresarequerimiento' || $tabla == 'certificadorequerimiento')
                $estado = ", idestado = 3 ";

            //  Si no es un requerimiento de cámara de seguridad se mueve al historial
            if($tabla !== 'camarasseguridad')
            {
                //  Creamos una entrada en el histórico de requerimientos
                $this->moveRequerimientoToHistorial($idRequerimiento, $tabla);
            }

            if( !is_null($fechaCaducidad) ){
                $fechaCaducidad = ", fechacaducidad = '" . $fechaCaducidad . "' ";
            }

        //  Actualizamos el ID del fichero por el nuevo
            $sqlUpdate = "update " . $tabla . " set idfichero = " . $idFicheroNuevo . ", updated = now() $estado $fechaCaducidad where id = " . $idRequerimiento;
            $this->getRepositorio()->queryRaw($sqlUpdate);
            return 'ok';
    }

    /**
     * Genera una entrada en el histórico de requerimientos
     */
    public function moveRequerimientoToHistorial($idRequerimiento, $tablaDestino)
    {

        //  Recuperamos la información del requerimiento
            $queryFields = [];
            $queryFields['getfields'] = '*';
            $queryFields['fields'] = [
                'id' => ' = ' . $idRequerimiento
            ];

            $requerimiento = $this->getByFields($queryFields, $tablaDestino);

        //  Si existe cogemos la información del requerimiento
            if($requerimiento)
            {

                    $requerimiento = $requerimiento[0];

                //  Recuperamos los datos del fichero
                    $queryFields = [];
                    $queryFields['getfields'] = '*';
                    $queryFields['fields'] = [
                        'id' => ' = ' . $requerimiento['idfichero']
                    ];
        
                    $resultFichero = $this->getByFields($queryFields, 'ficheroscomunes');

                    $nombreFichero = $resultFichero[0]['nombre'];
                    $nombreStorage = $resultFichero[0]['nombrestorage'];
                    $ubicacion = $resultFichero[0]['ubicacion'];

                //  Mapeamos los datos que vamos insertar en el histórico
                    $datosHistorico = [];
                    $datosHistorico['idrequerimiento'] = $requerimiento['idrequerimiento'];
                    $datosHistorico['idrelacionrequerimiento'] = (isset($requerimiento['id']) ? $requerimiento['id'] : null);
                    $datosHistorico['entidad'] = $tablaDestino;
                    $datosHistorico['fechasubida'] = (isset($requerimiento['fechasubida']) ? $requerimiento['fechasubida'] : null);
                    $datosHistorico['idfichero'] = $requerimiento['idfichero'];
                    $datosHistorico['idcomunidad'] = (isset($requerimiento['idcomunidad']) ? $requerimiento['idcomunidad'] : null);
                    $datosHistorico['idempleado'] = (isset($requerimiento['idempleado']) ? $requerimiento['idempleado'] : null);
                    $datosHistorico['idempresa'] = (isset($requerimiento['idempresa']) ? $requerimiento['idempresa'] : null);
                    $datosHistorico['fechacaducidad'] = (isset($requerimiento['fechacaducidad']) ? $requerimiento['fechacaducidad'] : null);
                    $datosHistorico['fechadescarga'] = (isset($requerimiento['fechadescarga']) ? $requerimiento['fechadescarga'] : null);

                //  Datos del fichero
                    $datosHistorico['nombre'] = $nombreFichero;
                    $datosHistorico['nombrestorage'] = $nombreStorage;
                    $datosHistorico['ubicacion'] = $ubicacion;
                    $datosHistorico['created_at'] = (isset($requerimiento['created']) ? $requerimiento['created'] : null);
                    $datosHistorico['observaciones'] = (isset($requerimiento['observaciones']) ? $requerimiento['observaciones'] : null);
                    $datosHistorico['usercreate'] = $this->getLoggedUserId();

                //  Inicializamos el controller de histórico
                    $this->InitController('Historico');

                //  Generamos el registro en el histórico de requerimientos
                    $this->HistoricoController->Create('ficheroshistorico', $datosHistorico);

            }
        
    }

    /** Comprueba si un requerimiento ya existe en el sistema */
    public function existeRequerimiento($idrequerimiento, $tablaDestino, $idkey, $id)
    {
        $tablasRelaciones = $this->getRelacionesTabla();
        return $this->getRepositorio()->ExisteRegistro($tablaDestino, " idrequerimiento=$idrequerimiento and $idkey = $id");
    }

    /** Recupera el listado de requerimientos según su tipología e id de comunidad */
    public function ListRequerimientoRGPD($destino, $_id)
    {

            $campoDestino = '';

        //  FIXME: Buscar forma más eficiente y menos peligrosa que esta
            switch(strtolower($destino)){
                case 'camarasseguridad':
                    $destino = 'CamarasSeguridad';
                    $campoDestino = 'idcomunidad';
                    break;
                case 'contratoscesion':
                    $destino = 'ContratosCesion';
                    $campoDestino = 'idcomunidad';
                    break;
                case 'rgpdempleado':
                    $destino = 'Rgpdempleado';
                    $campoDestino = 'usercreate';
                    $_id = $this->getLoggedUserId();
                    break;
            }

       //   Instanciamos el controller correspondiente
            $this->InitController( ucfirst($destino) );
            $modelo =  ucfirst($destino).'Controller';

        //  Recuperamos el listado general
        //  FIXME: Utilizar el filtro de resultados por idcomunidad
            $datos = $this->$modelo->List(null, false);
       
       //   Filtramos los datos por idcomunidad
            $datos = $this->filterResults($datos, $destino, $campoDestino, $_id);

        //  NOTE: ???Para aliviar la respuesta quitamos los datos de la comunidad ya que no los vamos a necesitar
        //    unset($datos[$destino]['comunidad']);

            return $datos;

    }

    /** Comprueba si una empresa ha asociado el documento de aceptación de operatoria a la comunidad */
    public function CheckOperatoriaEmpresaComunidad($idEmpresa, $idComunidad)
    {
        $sql = "
        SELECT 
            er.*, d.nombre as estado, f.nombre, f.nombrestorage, f.ubicacion
        FROM
            empresarequerimiento er
            left join documentoestado d on d.id = er.idestado
            left join ficheroscomunes f on f.id = er.idfichero
        WHERE
            er.idcomunidad = $idComunidad
            AND er.idempresa = $idEmpresa
            AND er.idrequerimiento = 1";

        $data = $this->query($sql);
        if(count($data)>0){
            $data = $data[0];
        }
        return $data;
    }

    /** Comprueba si el contrato entre administrador y comunidad está subido */
    public function CheckContratoAdministradorComunidad($idComunidad, $idAdministrador)
    {
        $sql = "
        SELECT 
            cr.*, d.nombre as estado, f.nombre, f.nombrestorage, f.ubicacion
        FROM
            comunidadrequerimiento cr
            left join documentoestado d on d.id = cr.idestado
            left join ficheroscomunes f on f.id = cr.idfichero
        WHERE
            cr.idcomunidad = $idComunidad
            AND cr.idadministrador = $idAdministrador
            AND cr.idrequerimiento = 32";

        $data = $this->query($sql);
        if(count($data)>0){
            $data = $data[0];
        }
        return $data;   
    }

    /** Refleja la descarga de un fichero en base de datos */
    public function SaveDescargaFichero()
    { 

        if($this->CheckFilePreviouslyDownloaded())
        {
            $this->UpdateFileDownload();
        }else{
            $this->InsertFileDownload();
        }

        return 'ok';

    }

    /** Recupera la fecha de descarga de un fichero */
    public function GetDateFileDownloaded($idFichero, $idUsuario, $idComunidad = -1)
    {

        $query = [];

        //  Campos de la consulta con sus valores
        $query['fields'] = [
            "idfichero" => $this->getIdFichero(),
            "idusuario" => $this->getIdUsuario(),
            "idcomunidad" => $this->getIdComunidad(),
            "idempresa" => $this->getIdEmpresa()
        ];

        //  Campos que se quiere recuperar de la consulta
        $query['getfields'] = ["id", "idfichero", "idusuario", "idcomunidad", "idempresa", "fechadescarga"];

        $result = $this->getByFields($query, 'ficherosdescargas');

        if(count( $result ) > 0)
        {
            return $result;
        }else{
            return null;
        }

    }

    /** Update file download date */
    private function UpdateFileDownload()
    {

        $sql = "update ficherosdescargas set fechaultimadescarga = now() where ";
        $sql .= " idfichero = " . $this->getIdFichero();
        $sql .= " and idcomunidad = " . $this->getIdComunidad();

        if($this->getIdEmpresa() != null){
            $sql .= " and idempresa = " . $this->getIdEmpresa();
        }

        $sql .= " and idusuario = " . $this->getIdUsuario();

        $this->queryRaw($sql);

    }

    /** Genera en el sistema la entrada de fichero descargado */
    private function InsertFileDownload()
    {

        $sql = "insert into ficherosdescargas(idfichero, idcomunidad, idempresa, idusuario, fechadescarga) values (";
        $sql.= $this->getIdFichero() . ", ";
        $sql.= $this->getIdComunidad() . ", ";

        if(!is_null($this->getIdEmpresa())){
            $sql.= $this->getIdEmpresa() . ", ";
        }

        $sql.= $this->getIdUsuario() . ", ";
        $sql .= "now() )";

        $this->queryRaw($sql);

    }

    /** Comprueba si un usuario ya ha descargado un documento del almacén */
    public function CheckFilePreviouslyDownloaded()
    {

        $fields = "idcomunidad = " . $this->getIdComunidad() . " and ";
        $fields .= "idempresa = " . $this->getIdEmpresa() . " and ";
        $fields .= "idusuario = " . $this->getIdUsuario() . " and ";
        $fields .= "idfichero = " . $this->getIdFichero();

        return $this->getRepositorio()->ExisteRegistro('ficherosdescargas', $fields);

    }

    /**
     * 
     */
    public function ListRequerimientosByEntity($entity, $idRequerimiento, $fieldId, $fieldValue)
    {
        //  Recupera todos los requerimientos
        // TODO: Hay que montar los respectivos controller/model/entity
        //       Se hace mediante Query por falta de tiempo en la entrega del software
        $sql = "
        SELECT 
            r.*, f.nombre,f.nombrestorage,f.ubicacion,f.estado,f.created fechasubida,f.updated fechaactualizacion,
            f.usercreate usuariocreacion
        FROM
            $entity"."requerimiento r
            LEFT JOIN ficheroscomunes f ON f.id = r.idfichero
        WHERE 
            r.$fieldId = $fieldValue
            AND r.idrequerimiento = $idRequerimiento
            order by f.id desc limit 1";
        
        return $this->query($sql);

    }

    /** Recupera los documentos pendientes de verificación */
    public function GetListadoDocumentosSujetosVerificacion()
    {
        $sql = "SELECT * FROM requerimiento where activado = 1 and sujetorevision = 1";
        return $this->query($sql);
    }

    /** Recupera los documentos que están en estado pendiente de verificación */
    public function GetDocumentosPendientesVerificacion($destino = null)
    {

        $sql = "
            SELECT 
                er.id idrelacionrequerimiento, er.idrequerimiento, er.idfichero, er.idempresa, er.fechacaducidad,
                e.razonsocial nombreempresa, '' as nombreempleado, c.nombre as comunidad, e.cif, e.idtipoempresa, f.nombre as nombrefichero, f.nombrestorage, f.ubicacion, f.created,
                r.nombre as nombrerequerimiento, r.caduca, r.sujetorevision, r.requieredescarga, 'empresarequerimiento' as entidad
            FROM 
                empresa e,
                empresarequerimiento er
                LEFT JOIN comunidad c on c.id = er.idcomunidad,
                ficheroscomunes f,
                requerimiento r
            where 
                e.idusuario = er.idempresa
                and f.id = er.idfichero
                and er.idestado = 3
                and r.id = er.idrequerimiento
        union all
            SELECT 
                er.id idrelacionrequerimiento, er.idrequerimiento, er.idfichero, er.idempleado, er.fechacaducidad,
                '' as nombreempresa, e.nombre as nombreempleado, c.nombre as comunidad, e.numerodocumento as cif, 3 as idtipoempresa, f.nombre as nombrefichero, f.nombrestorage, f.ubicacion, f.created,
                r.nombre as nombrerequerimiento, r.caduca, r.sujetorevision, r.requieredescarga, 'empleadorequerimiento' as entidad
            FROM 
                empleado e,
                empleadorequerimiento er
                LEFT JOIN comunidad c on c.id = er.idcomunidad,
                ficheroscomunes f,
                requerimiento r
            where 
                e.id = er.idempleado
                and f.id = er.idfichero
                and er.idestado = 3
                and r.id = er.idrequerimiento
        order by created asc";
        return $this->query($sql);

    }

    public function GetListadoDocumentosComunidadDescargadosEmpresa($idUsuario, $idEmpresa, $idComunidad)
    {
        $sql = "
            SELECT 
                r.nombre, fd.fechadescarga, fd.fechaultimadescarga
            FROM 
                requerimiento r 
                left join comunidadrequerimiento cr on cr.idrequerimiento = r.id and cr.idcomunidad = $idComunidad
                left join ficherosdescargas fd on fd.idfichero = cr.idfichero and fd.idempresa = $idEmpresa and fd.idcomunidad = $idComunidad
            where 
                r.tipo = 'COM'
                and r.activado = 1";

        return $this->query($sql);
    }

    /** Recupera la información de descarga para un requerimiento por usuario */
    public function GetInfoDescargaRequerimiento($idUsuario, $idFichero, $idEmpresa = null, $idComunidad = null)
    {

        $infoDescarga = [];
        $infoDescarga['id'] = null;
        $infoDescarga['idfichero'] = null;
        $infoDescarga['idcomunidad'] = null;
        $infoDescarga['idempresa'] = null;
        $infoDescarga['idusuario'] = null;
        $infoDescarga['fechadescarga'] = null;
        $infoDescarga['fechaultimadescarga'] = null;

        $sql = "select * from ficherodescargas where idusuario=$idUsuario and idfichero=$idFichero ";
        if(!is_null($idEmpresa))
            $sql .= " and idempresa = $idEmpresa ";

        if(!is_null($idComunidad))
            $sql .= " and idcomunidad = $idComunidad";

        $result = $this->query($sql);

        if(count($result) > 0)
        {
            return $result[0];
        }

        return $infoDescarga;

    }

    /** Devuelve el tipo de empresa que es: 1 Empresa 2 Autónomo  */
    public function GetTipoContratista($idEmpresa)
    {
        return $this->getRepositorio()->getValue('empresa', 'idtipoempresa', $idEmpresa, 'idusuario');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                     REQUERIMIENTOS PENDIENTES CAE
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /** Recupera todos los requerimientos pendientes de CAE para un administrador y que por cada comunidad tenga contratado el servicio */
    public function GetRequerimientosPendientesCAE($idAdministrador, $idComunidad = null, $_empresasAsociadas = false)
    {
        //  Recuperamos los requerimientos de CAE de comunidad por cada
        //  una de las comunidades del administrador
        $whereComunidad = "";

        if(!is_null($idComunidad))
        {
            $whereComunidad = " where reqcae.idcomunidad = $idComunidad ";
        }

        $sql = "
        select * from (
        select * from (select reqcae.*, cr.id as comunidad_idrequerimiento, '1' as contratado, 1 as idservicio from 
                (SELECT 
                    c.id as idcomunidad,
                    concat(c.codigo, ' - ', c.nombre) as comunidad,
                    c.codigo as codigocomunidad, 
                    r.id as idrequerimiento, r.nombre as requerimiento, r.orden
                FROM
                    comunidadservicioscontratados csc,
                    comunidad c left join requerimiento r on r.idrequerimientotipo = 6 and r.activado = 1
                WHERE
                    csc.idcomunidad = c.id and csc.idservicio = 1 and csc.contratado = 1 
                    and c.usuarioid = $idAdministrador
                    and c.estado = 'A'
                    order by c.nombre asc) as reqcae
                    left join comunidadrequerimiento cr on cr.idcomunidad = reqcae.idcomunidad and cr.idrequerimiento = reqcae.idrequerimiento
                    #left join comunidadservicioscontratados csc on csc.idcomunidad = reqcae.idcomunidad and csc.idservicio = 1 and csc.contratado = 1
                where 
                    cr.idfichero is null or cr.estado in(1, 7)
                ) as reqcae
           $whereComunidad ";
        if($_empresasAsociadas)
        {
            $sql .= "
                union all
                    select 
                        c.id as idcomunidad, concat(c.codigo, ' - ', c.nombre) as comunidad,
                        c.codigo as codigocomunidad, 100 as idrequerimiento, 'No tiene proveedor asignado' as requerimiento,
                        null as comunidad_idrequerimiento, 1 as contratado, 1 as idservicio, 9 as orden
                    from 
                        comunidad c,
                        comunidadservicioscontratados csc";
                    if(!is_null($idComunidad))
                    {
                        $sql .= " where c.id = $idComunidad and ";
                    }else{
                        $sql .= " where ";
                    }
                    $sql .= "
                        c.estado = 'A' and c.usuarioId = $idAdministrador
                        and c.id not in (
                            SELECT c1.id
                            FROM
                                comunidad c1, comunidadempresa ce
                            where ce.idcomunidad = c1.id  )
                        and csc.idcomunidad = c.id and csc.idservicio = 1 and csc.contratado = 1  ";
        }                  
            $sql.= "          ) tabla order by tabla.comunidad asc, tabla.orden asc";

        // die($sql);
        return $this->query($sql);
    }

    public function GetRequerimientosPendientesCAEGeneral()
    {
        $sql = "SELECT 
        comunidadescae.*,
        u.nombre,
        u.telefono,
        u.email,
        u.emailcontacto
    FROM
        (SELECT 
        c.id AS comunidadid,
        c.nombre AS comunidad,
        c.codigo AS codigocomunidad,
        c.cif as cifcomunidad,
        c.direccion as direccioncomunidad,
        c.codpostal as codpostalcomunidad,
        c.localidad as localidadcomunidad,
        c.provincia as provinciacomunidad,
        GROUP_CONCAT(r.nombre ORDER BY r.nombre ASC SEPARATOR ', ') AS requerimientos
    FROM
        comunidadservicioscontratados csc
    INNER JOIN comunidad c ON csc.idcomunidad = c.id
    LEFT JOIN requerimiento r ON r.idrequerimientotipo = 6 AND r.activado = 1
    LEFT JOIN comunidadrequerimiento cr ON cr.idcomunidad = c.id AND cr.idrequerimiento = r.id AND cr.idestado = 4
    WHERE
        csc.idservicio = 1
        AND csc.contratado = 1
        AND c.estado = 'A'
        AND cr.idfichero IS NULL  
    GROUP BY c.id, c.nombre, c.codigo, c.cif, c.direccion, c.codpostal, c.localidad, c.provincia
    ORDER BY comunidadid ASC) comunidadescae,
        usuario u,
        comunidad c
    WHERE
        c.id = comunidadescae.comunidadid
        AND u.id = c.usuarioid";
            return $this->query($sql);
    }

    /** Recupera todos los requerimientos pendientes de CAE para un administrador y que por cada comunidad tenga contratado el servicio */
    public function GetRequerimientosPendientesCAEEmpresa($idAdministrador)
    {
        //  Recuperamos las comunidades que tiene el administrador asignado

        //  Recuperamos las empresas que tienen asignadas las comunidades del administrador

        //  Sobre esas comunidades recuperamos aquellos documentos que están pendientes de subir o pendientes de revisión

        //  Comprobar si la comunidad tiene empresas asignadas
        $sql = "
        select * from (
            select * from 
            (SELECT 
                e.*
            FROM
                comunidad c,
                comunidadempresa ce,
                empresa e
            WHERE
                ce.idcomunidad = c.id
                and e.id = ce.idempresa
                and c.usuarioid = $idAdministrador
                and c.estado = 'A' group by ce.idempresa) empresas_administrador
        left join 
            (SELECT 
                c.id as idcomunidad,
                c.id as idempresa,
                c.razonsocial as comunidad,
                '' as codigocomunidad, 
                r.id as idrequerimiento, r.nombre as requerimiento, r.orden
            FROM
                empresa c left join requerimiento r on r.idrequerimientotipo = 4 and r.activado = 1
            WHERE
                c.estado = 'A'
            ) vdce on vdce.idempresa = empresas_administrador.id
        ) t1 left join empresarequerimiento er on er.idempresa = t1.idusuario and er.idrequerimiento = t1.idrequerimiento 
		where 
			(er.idfichero is null or er.idestado in(1,3,7))
        order by razonsocial asc, orden asc
        ";

        $result = $this->query($sql);
        if(count($result) > 0)
        {
            //  Comprobamos si la empresa tiene empleados dados de alta
            // $this->InitController('Empresa');
            $tmpEmpresa = '';
            $empleadosCalculados = false;
            $reqEmpleados = [];

            //  Por cada una de las empresas comprobamos si tiene empleados dados de alta
            for($x = 0; $x < count($result); $x++)
            {

                //  Comprobamos si ya hemos recuperado el nº de empleados por esta empresa
                if($x == 0)
                {
                    $tmpEmpresa = $result[$x]['idusuario'];
                }else{
                    $empleadosCalculados = true;

                    if($result[$x]['idusuario'] != $tmpEmpresa)
                    {
                        $tmpEmpresa = $result[$x]['idusuario'];
                        $empleadosCalculados = false;
                    }else{
                        $empleadosCalculados = true;
                    }

                }

                //  Comprobamos si tiene empleados dados de alta
                if(!$empleadosCalculados)
                {

                    $nEmpleados = $this->getRepositorio()->selectCount('empleadoempresa','idempresa', '=', $result[$x]['idusuario']);

                    if($nEmpleados == 0)
                    {

                        $requerimientoEmpleado = [];
                        $requerimientoEmpleado['id'] = $result[$x]['id'];
                        $requerimientoEmpleado['razonsocial'] = $result[$x]['razonsocial'];
                        $requerimientoEmpleado['cif'] = $result[$x]['cif'];
                        $requerimientoEmpleado['telefono'] = $result[$x]['telefono'];
                        $requerimientoEmpleado['personacontacto'] = $result[$x]['personacontacto'];
                        $requerimientoEmpleado['email'] = $result[$x]['email'];
                        $requerimientoEmpleado['direccion'] = $result[$x]['direccion'];
                        $requerimientoEmpleado['idlocalidad'] = $result[$x]['idlocalidad'];
                        $requerimientoEmpleado['provinciaid'] = $result[$x]['provinciaid'];
                        $requerimientoEmpleado['localidad'] = $result[$x]['localidad'];
                        $requerimientoEmpleado['codpostal'] = $result[$x]['codpostal'];
                        $requerimientoEmpleado['idtipoempresa'] = $result[$x]['idtipoempresa'];
                        $requerimientoEmpleado['idusuario'] = $result[$x]['idusuario'];
                        $requerimientoEmpleado['created'] = $result[$x]['created'];
                        $requerimientoEmpleado['updated'] = $result[$x]['updated'];
                        $requerimientoEmpleado['usercreate'] = $result[$x]['usercreate'];
                        $requerimientoEmpleado['estado'] = $result[$x]['estado'];
                        $requerimientoEmpleado['idcomunidad'] = $result[$x]['idcomunidad'];
                        $requerimientoEmpleado['idempresa'] = $result[$x]['idempresa'];
                        $requerimientoEmpleado['comunidad'] = $result[$x]['comunidad'];
                        $requerimientoEmpleado['codigocomunidad'] = $result[$x]['codigocomunidad'];
                        $requerimientoEmpleado['idrequerimiento'] = $result[$x]['idrequerimiento'];
                        $requerimientoEmpleado['requerimiento'] = 'La empresa no tiene empleados dados de alta en Fincatech';
                        $requerimientoEmpleado['orden'] = 9;
                        $requerimientoEmpleado['idestado'] = $result[$x]['idestado'];
                        $requerimientoEmpleado['idfichero'] = $result[$x]['idfichero'];
                        $requerimientoEmpleado['fechacaducidad'] = $result[$x]['fechacaducidad'];
                        $requerimientoEmpleado['observaciones'] = $result[$x]['observaciones'];
                        $reqEmpleados[] = $requerimientoEmpleado;
                    }

                }

            }

        }

        if(count($reqEmpleados) > 0)
        {
            $result = array_merge($result, $reqEmpleados);
            usort($result, function($a, $b) {
                // return $a['idusuario'] > $b['idusuario'];
                if ($a['idusuario'] == $b['idusuario']) {
                    return 0;
                }
                return ($a['idusuario'] < $b['idusuario']) ? -1 : 1;                
            });
        }

        return $result;

    }

    public function GetRequerimientosCAEEmpresa($idEmpresa)
    {
        $sql = "SELECT * FROM view_documentoscaeempresa where @p1:=" . $idEmpresa;
        return $this->query($sql); 
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///                                     REQUERIMIENTOS PENDIENTES DPD
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /** Recupera todos los requerimientos pendientes en materia de RGPD para un administrador */
    public function GetRequerimientosPendientesRGPD($idAdministrador)
    {

        //  Contrato entre admin y comunidad
        $sql = "select * from (
                    SELECT 
                        c.id as idcomunidad, concat(c.codigo, ' - ', c.nombre) as comunidad, c.codigo as codigocomunidad,
                        'Contrato administración de Fincas con Comunidad de Propietarios' as requerimiento
                    FROM
                        comunidad c
                        left join comunidadrequerimiento cr on cr.idrequerimiento = 32 and cr.idcomunidad = c.id
                    WHERE
                        c.usuarioid = $idAdministrador AND c.estado = 'A'
                        and cr.idcomunidad is null or cr.estado = 1            
                UNION ALL ";


        //  Recuperamos los Contratos administración entre comunidad y administrador
        $sql .= "
            select 
                #cr.idcomunidad, cr.idfichero, cr.idestado, cr.estado, cr.created, cr.updated, cr.id as comunidad_idrequerimiento,
                c.id as idcomunidad, concat(c.codigo, ' - ', c.nombre) as comunidad, c.codigo as codigocomunidad,
                r.nombre as requerimiento
                #r.id as idrequerimiento, r.nombre as requerimiento
            from 
                comunidadrequerimiento cr
                left join requerimiento r on r.id = cr.idrequerimiento,
                comunidad c
            where 
                r.idrequerimientotipo in(3,9)
                and c.id = cr.idcomunidad
                and cr.estado = 1
                and c.usuarioId = $idAdministrador
                and c.estado = 'A'
            UNION ALL ";

        //  Cámaras de seguridad por comunidad
        $sql .= "
            select 
                c.id as idcomunidad, concat(c.codigo, ' - ', c.nombre) as comunidad, c.codigo as codigocomunidad,
                cs.titulo as requerimiento
            from 
                camarasseguridad cs,
                comunidad c
            where 
                c.id = cs.idcomunidad
                and (cs.idfichero = -1 or cs.idfichero is null)
                and c.usuarioId = $idAdministrador
                and c.estado = 'A'
            UNION ALL ";

        //  Contratos de cesión de datos a terceros por comunidad

        $sql .= "
        select 
            c.id as idcomunidad, concat(c.codigo, ' - ', c.nombre) as comunidad, c.codigo as codigocomunidad,
            cc.titulo as requerimiento
        from 
            contratoscesion cc,
            comunidad c
        where 
            c.id = cc.idcomunidad
            and (cc.idfichero = -1 or cc.idfichero is null)
            and c.usuarioId = $idAdministrador
            and c.estado = 'A'
        ) as tabla
        order by tabla.comunidad asc";

        return $this->query($sql);

    }

    /** Recupera todos los requerimientos pendientes de RGPD para una comunidad */
    public function GetRequerimientosPendientesRGPDComunidad($idComunidad, $idAdministrador, $_camarasSeguridad = false)
    {

            $totalRequerimientosPendientes = 0;
        
        //  Comprobamos si ha adjuntado el Contrato administración de Fincas con Comunidad de Propietarios
            $totalRequerimientosPendientes = (count($this->CheckContratoAdministradorComunidad($idComunidad, $idAdministrador)) > 0 ? 0 : 1);

        //  Comprobamos los requerimientos de cámaras de seguridad que tenga subidos a la comunidad si la comunidad tiene cámaras de seguridad
            if( boolval($_camarasSeguridad) )
                $totalRequerimientosPendientes += $this->GetTotalRequerimientosPendientesCamarasSeguridad($idComunidad);
        
        //  Comprobamos los requerimientos de contratos de cesión a terceros que tenga subidos a una comunidad
            $totalRequerimientosPendientes += count($this->GetRequerimientosPendientesRGPDContratosCesionDatosTerceros($idComunidad));
    
        //  Devolvemos el total
            return $totalRequerimientosPendientes;

    }

    /** Calcula el número de requerimientos pendientes por rgpd de empleados del administrador */
    public function GetNumeroRequerimientosPendientesRGPDEmpleados($idAdministrador)
    {
        return $this->getRepositorio()->selectCount('rgpdempleado', 'usercreate', '=', $idAdministrador . " and idfichero is null");
    }

    /** Recupera los requerimientos RGPD de tipo Contratos Cesión Datos terceros para una comunidad */
    public function GetRequerimientosPendientesRGPDContratosCesionDatosTerceros($idComunidad)
    {
        $sql = "SELECT * FROM contratoscesion where idcomunidad = $idComunidad and idfichero = -1";
        return $this->query($sql);
    }

    /** Obtiene el total de requerimientos de tipo RGPD por comunidad para calcular el % de cumplimiento */
    public function GetTotalRequerimientosRGPD($idAdministrador, $idComunidad, $_camarasSeguridad= false)
    {
        
        //  Se inicializa a 1 porque es obligatorio el contrato administración de fincas con comunidad de propietarios
            $totalRequerimientos = 1;

        //  Comprobamos los requerimientos de cámaras de seguridad que tenga subidos a la comunidad si la comunidad tiene cámaras de seguridad
            if( boolval($_camarasSeguridad) )
                $totalRequerimientos += $this->GetTotalRequerimientosCamarasSeguridad($idComunidad);
            
        //  Comprobamos los requerimientos de contratos de cesión a terceros que tenga subidos a una comunidad
            $totalRequerimientos += count($this->GetTotalRequerimientosRGPDContratosCesionDatosTerceros($idComunidad));
            
        //  Devolvemos el total
            return $totalRequerimientos;
    }

    /** Recupera los requerimientos RGPD de tipo Contratos Cesión Datos terceros para una comunidad */
    public function GetTotalRequerimientosRGPDContratosCesionDatosTerceros($idComunidad)
    {

        $sql = "SELECT * FROM contratoscesion where idcomunidad = $idComunidad";
        return $this->query($sql);

    }

    /** Devuelve el número total de requerimientos de cámaras de seguridad para una comunidad
     * @param int ID Comunidad
     * @return int Total de documentos pendientes de subir para cámaras de seguridad
     */
    public function GetTotalRequerimientosCamarasSeguridad($idComunidad)
    {
        //ID Requerimiento Registro de actividades de tratamiento: 42
        return $this->getRepositorio()->selectCount('camarasseguridad', 'idcomunidad', '=', $idComunidad);
    }

    /** Devuelve el número total de requerimientos pendientes de cámaras de seguridad
     * @param int ID Comunidad
     * @return int Total de documentos pendientes de subir para cámaras de seguridad
     */
    public function GetTotalRequerimientosPendientesCamarasSeguridad($idComunidad)
    {
        //ID Requerimiento Registro de actividades de tratamiento: 42
        return $this->getRepositorio()->selectCount('camarasseguridad', 'idcomunidad', '=', $idComunidad . " and idfichero = -1");
    }

    /** Devuelve los requerimientos caducados de empleados */
    public function GetRequerimientosCaducadosEmpleado()
    {
        $sql = "
            SELECT 
            e.nombre,
            e.email, e.telefono, tpe.nombre puesto,
            r.nombre requerimiento, date_format(timestamp(er.fechacaducidad),'%d-%m-%Y') fecha,
            date_format(timestamp(er.created),'%d-%m-%Y') fecha_subida,
            emp.idempresa, em.razonsocial empresa, em.personacontacto empresa_personacontacto, em.email empresa_email
            #,em.*
        FROM
            empleadorequerimiento er,
            empleado e,
            empresa em,
            empleadoempresa emp,
            tipopuestoempleado tpe,
            requerimiento r
        WHERE
            er.fechacaducidad IS NOT NULL
            AND DATE(er.fechacaducidad) <= DATE(NOW())
            and e.id = er.idempleado
            and tpe.id = e.idtipopuestoempleado
            and r.id = er.idrequerimiento
            and emp.idempleado = e.id
            and em.idusuario = emp.idempresa
        ORDER BY idempresa ASC, requerimiento ASC";
        return  $this->query($sql);
    }

    /** Devuelve los requerimientos caducados de CAE para una empresa */
    public function GetRequerimientosCaducadosEmpresa()
    {

    }

    public function comprobarDocumentacionComunidad($idComunidad)
    {

    }

    /** Recupera el último e-mail certificado enviado a una empresa por comunidad */
    public function GetEmailCertificadoEmpresaComunidad($idEmpresa, $idComunidad)
    {
        $sql = "select filename from emailscertificados where idempresa = $idEmpresa and idcomunidad = $idComunidad order by id desc limit 1";
        return $this->query($sql);
    }

}