<?php

namespace Fincatech\Model;

use HappySoftware\Database\DatabaseCore;

//use Fincatech\Entity\Usuario\Usuario;

class CertificadodigitalModel extends \HappySoftware\Model\Model{

    private $entidad = 'CertificadoDigital';

    private $tablasSchema = array("usuario", "usuarioRol");

    private $_id;
    private $_idComunidad;
    private $_idAdministrador;
    private $_fechaSolicitud;
    private $_aprobado;
    private $_solicitadoUanataca;
    private $_solicitudCertificado;
    private $_fechaAprobacion;
    private $_requerimientosIdS;
    private $_estado;
    private $_created;
    private $_usercreate;
    private $_userapproved;
    private $_idRepresentanteLegal;

    public function Id(){return $this->_id;}
    public function IdComunidad(){return $this->_idComunidad;}
    public function IdAdministrador(){return $this->_idAdministrador;}
    public function IdRepresentanteLegal(){ return $this->_idRepresentanteLegal;}    
    public function FechaSolicitud(){return $this->_fechaSolicitud;}
    public function Aprobado(){return $this->_aprobado;}
    public function SolicitadoUanataca(){return $this->_solicitadoUanataca;}
    public function FechaAprobacion(){return $this->_fechaAprobacion;}
    public function RequerimientosIdS(){return $this->_requerimientosIdS;}
    public function Estado(){return $this->_estado;}
    public function Created(){return $this->_created;}
    public function Usercreate(){return $this->_usercreate;}
    public function Userapproved(){return $this->_userapproved;}
    public function SolicitadCertificado(){return $this->_solicitudCertificado;}

    public function SetId($value){
        $this->_id = $value;
        return $this;
    }
    public function SetIdComunidad($value){
        $this->_idComunidad = $value;
        return $this;
    }
    public function SetIdAdministrador($value){
        $this->_idAdministrador = $value;
        return $this;
    }
    public function SetIdRepresentanteLegal($value){
        $this->_idRepresentanteLegal = $value;
        return $this;
    }    
    public function SetFechaSolicitud($value){
        $this->_fechaSolicitud = $value;
        return $this;
    }
    public function SetAprobado($value){
        $this->_aprobado = $value;
        return $this;
    }
    public function SetSolicitadoUanataca($value){
        $this->_solicitadoUanataca = $value;
        return $this;
    }
    public function SetFechaAprobacion($value){
        $this->_fechaAprobacion = $value;
        return $this;
    }
    public function SetRequerimientosIdS($value){
        $this->_requerimientosIdS = $value;
        return $this;
    }
    public function SetEstado($value){
        $this->_estado = $value;
        return $this;
    }
    public function SetCreated($value){
        $this->_created = $value;
        return $this;
    }
    public function SetUsercreate($value){
        $this->_usercreate = $value;
        return $this;
    }
    public function SetUserapproved($value){
        $this->_userapproved = $value;
        return $this;
    }

    public function SetSolicitudCertificado($value){
        $this->_solicitudCertificado = $value;
        return $this;
    }

    /**
     * @var \Fincatech\Entity\Usuario\Usuario
     */
    public $certificadoDigital;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        //$this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    /**
     * Genera la solicitud en base de datos
     */
    public function Insert()
    {
        $datos = [];
        $datos['idcomunidad'] = $this->IdComunidad();
        $datos['idrepresentante'] = $this->IdRepresentanteLegal();
        $datos['fechasolicitud'] = date('Y-m-d H:m:i');
        $datos['aprobado'] = 0;
        $datos['solicitadouanataca'] = 0;
        $datos['solicitudcertificado'] = 0;
        $datos['requerimientosIdS'] = $this->RequerimientosIdS();
        $datos['estado'] = 'P';
        return $this->Create('comunidadcertificado', $datos)['id'];
    }

    public function GetCertificateRequestByComunityId($idComunidad)
    {
        $queryFields = [];
        $queryFields['getfields'] = '*';
        $queryFields['fields'] = [
            'idcomunidad' => ' = ' . $idComunidad
        ];
        $resultado = $this->getByFields($queryFields, 'comunidadcertificado');        
        return $resultado;
    }

    /** Listado de comunidades que han solicitado el certificado
     * 
     */
    public function GetComunidadesCertificado($aprobado = true, $solicitadoUanataca = false, $useLoggedUserId = false, $getAll = false)
    {
        $sql = "
            select
            c.id, c.codigo, c.nombre, 
            concat(u.nombre, ' ', u.apellido, ' ', u.apellido2) as 'administrador', u.documento, u.telefono, u.email,
            cc.id as 'idsolicitud', cc.fechasolicitud, cc.fechaaprobacion,
            ua.nombre as tecnicoaprobacion,
            cc.aprobado, cc.solicitadouanataca, cc.solicitudcertificado, cc.uanatacaid
        from 
            comunidadcertificado cc 
            left join usuario ua on ua.id = cc.userapproved,
            comunidad c,
            representantelegal u
        where 
            u.id = cc.idrepresentante and
            c.id = cc.idcomunidad ";

        //  Si se deben recoger todas independientemente del estado de la solicitud
        if(!$getAll){
            $sql .= " and cc.aprobado = " . ($aprobado ? 1 : 0);
        }
        
        $sql .= " and cc.solicitadouanataca = " . ($solicitadoUanataca ? 1 : 0);
        
        //  Restricción para mostrar únicamente las solicitudes del administrador autenticado en el sistema
        if($useLoggedUserId){
            $sql .= " and cc.usercreate = " . $this->getLoggedUserId();
        }

        $sql .=  " order by cc.fechasolicitud asc ";
        return $this->query($sql);
    }

    /** Marca una solicitud como aprobada por parte del técnico de certificados digitales */
    public function AprobarSolicitudCertificado($idComunidad){
        $sql = "update comunidadcertificado set aprobado = 1, fechaaprobacion = now(), estado = 'A' , userapproved = " . $this->getLoggedUserId() . " where idcomunidad = ";
        $idComunidad = DatabaseCore::PrepareDBString($idComunidad);
        $sql .= $idComunidad;
        $this->queryRaw($sql);
    }

    /** * Actualiza una solicitud marcándola como solicitado Certificado por parte del administrador */
    public function AdministradorSolicitaCertificado($idSolicitud)
    {
        $idSolicitud = DatabaseCore::PrepareDBString($idSolicitud);
        $sql = "update comunidadcertificado set solicitudcertificado = 1 where id = " . $idSolicitud;
        $this->queryRaw($sql);
    }

    /** Recupera el ID de la solicitud del certificado para una comunidad */
    public function SolicitudCertificado($idComunidad)
    {
        $sql = "select * from comunidadcertificado where idcomunidad = " . $idComunidad;
        $resultado = $this->query($sql);
        if(count($resultado))
        {
            return $resultado;
        }else{
            return null;
        }
    }

    /**
     * Asigna el ID de uanataca para un documento adjunto a la solicitud realizada
     * @param int $fileId ID del fichero
     * @param int $uanatacaId ID de Uanataca
     */
    public function UpdateUanatacaIdFile($fileId, $uanatacaId)
    {
        $sql = 'update certificadorequerimiento set uanatacaid = ' . $uanatacaId . ' where idfichero = ' . $fileId;
        $this->queryRaw($sql);
    }

    /**
     * Asigna el ID de uanataca para una solicitud realizada
     * @param int $idComunidad ID de la comunidad
     * @param int $uanatacaId ID de Uanataca
     */    
    public function SetUanatacaIdSolicitudCertificadoComunidad($idComunidad, $uanatacaId)
    {
        $sql = 'update comunidadcertificado set aprobado=1, solicitadouanataca=1, uanatacaid = ' . $uanatacaId . ' where idcomunidad = ' . $idComunidad;
        $this->queryRaw($sql);
    }

    /** Devuelve la información de la solicitud de certificado digital de una comunidad */
    public function InfoSolicitud($idComunidad)
    {
        $sql = "SELECT 
                    cc.id, cc.idcomunidad, cc.idrepresentante, cc.fechasolicitud, cc.fechaaprobacion,
                    concat(r.nombre, ' ', r.apellido, ' ', r.apellido2) representante, r.email representanteemail,
                    u.nombre administrador, u.email emailadministrador,
                    c.codigo codigocomunidad, c.nombre comunidad    
                FROM
                    comunidad c,
                    comunidadcertificado cc,
                    representantelegal r,
                    usuario u
                WHERE
                    cc.idcomunidad = $idComunidad
                    and c.id = cc.idcomunidad
                    and r.id = cc.idrepresentante
                    and u.id = r.administradorid";
        return $this->query($sql);
    }

}