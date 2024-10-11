<?php

namespace Fincatech\Model;

use Fincatech\Entity\Usuario\Usuario;
use HappySoftware\Database\DatabaseCore;

class AdministradorModel extends \HappySoftware\Model\Model{

    private $entidad = 'Usuario';

    //  Tabla donde almacenamos los documentos identificativos de administradores
    private $tablaDocumento = 'admindocument';

    private $tablasSchema = array("usuario, usuarioRol");

    public $administrador;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    /** Devuelve el número de comunidades que tiene un administrador */
    public function GetNumeroComunidades($administradorId)
    {
        return $this->getRepositorio()->selectCount('comunidad','usuarioId','=',$administradorId . " and estado='A' ");
    }

    public function ProcessArrayDataToExcel($datosComunidad)
    {
        //  Datos del fichero
        /*
          codigo, nombre, cif, direccion, codpostal, localidad, provincia, ibancomunidad, 
          cae contratado, cae pvp, cae precio comunidad, 
          rgpd contratado, rgpd pvp, rgpd precio comunidad, 
          prl.contratado, prl.pvp,	prl.preciocomunidad	
          instalaciones.contratado	instalaciones.pvp	instalaciones.preciocomunidad	
          certificadosdigitales.contratado	certificadosdigitales.pvp	certificadosdigitales.preciocomunidad
        */
            $datosParseados = [];
        //  Cabecera del fichero
            $datosParseados[0] = [];
            $datosParseados[0]['col'] = 'Código';
            $datosParseados[0]['col1'] = 'Nombre';
            $datosParseados[0]['col2'] = 'CIF';
            $datosParseados[0]['col3'] = 'Dirección';
            $datosParseados[0]['col4'] = 'Código postal';
            $datosParseados[0]['col5'] = 'Localidad';
            $datosParseados[0]['col6'] = 'Provincia';
            $datosParseados[0]['col7'] = 'IBAN Comunidad';
        //  CAE
            $datosParseados[0]['col8'] = 'Cae Contratado';
            $datosParseados[0]['col9'] = 'Coste Cae';
            $datosParseados[0]['col10'] = 'Cae Precio Comunidad';
            $datosParseados[0]['col11'] = 'Cae Fecha contratación';
        //  RGPD
            $datosParseados[0]['col12'] = 'RGPD Contratado';
            $datosParseados[0]['col13'] = 'RGPD PVP';
            $datosParseados[0]['col14'] = 'RGPD Precio Comunidad';        
            $datosParseados[0]['col15'] = 'RGPD Fecha contratación';        
        //  PRL
            $datosParseados[0]['col16'] = 'PRL Contratado';
            $datosParseados[0]['col17'] = 'PRL PVP';
            $datosParseados[0]['col17'] = 'PRL Precio Comunidad';  
            $datosParseados[0]['col18'] = 'PRL Fecha contratación';        

        //  instalaciones
            $datosParseados[0]['col19'] = 'Instalaciones Contratado';
            $datosParseados[0]['col20'] = 'Instalaciones PVP';
            $datosParseados[0]['col21'] = 'Instalaciones Precio Comunidad';  
            $datosParseados[0]['col22'] = 'Instalaciones Fecha contratación';        

        //  Certificados digitales
            $datosParseados[0]['col23'] = 'Certificados digitales Contratado';
            $datosParseados[0]['col24'] = 'Certificados digitales PVP';
            $datosParseados[0]['col25'] = 'Certificados digitales Precio Comunidad';                  
            $datosParseados[0]['col26'] = 'Certificados digitales Fecha contratación'; 
        
        //  Estado de la comunidad
            $datosParseados[0]['col27'] = 'Estado';

        for($x = 0; $x < count($datosComunidad); $x++)
        {

                $i = $x+1;

                $datosParseados[$i]['col'] = $datosComunidad[$x]['codigo'];
                $datosParseados[$i]['col1'] = $datosComunidad[$x]['comunidad'];
                $datosParseados[$i]['col2'] = $datosComunidad[$x]['cif'];
                $datosParseados[$i]['col3'] = $datosComunidad[$x]['direccion'];
                $datosParseados[$i]['col4'] = $datosComunidad[$x]['codpostal'];
                $datosParseados[$i]['col5'] = $datosComunidad[$x]['localidad'];
                $datosParseados[$i]['col6'] = $datosComunidad[$x]['provincia'];
                $datosParseados[$i]['col7'] = $datosComunidad[$x]['ibancomunidad'];
            //  CAE
                $datosParseados[$i]['col8'] = ($datosComunidad[$x]['cae_contratado'] == true ? 'S' : 'N');
                $datosParseados[$i]['col9'] = $datosComunidad[$x]['cae_precio'];
                $datosParseados[$i]['col10'] = $datosComunidad[$x]['cae_preciocomunidad'];
                $datosParseados[$i]['col11'] = date('d/m/Y',strtotime($datosComunidad[$x]['cae_fechaalta']));
            //  RGPD
                $datosParseados[$i]['col12'] = ($datosComunidad[$x]['rgpd_contratado'] == true ? 'S' : 'N');
                $datosParseados[$i]['col13'] = $datosComunidad[$x]['rgpd_precio'];
                $datosParseados[$i]['col14'] = $datosComunidad[$x]['rgpd_preciocomunidad'];        
                $datosParseados[$i]['col15'] = date('d/m/Y', strtotime($datosComunidad[$x]['rgpd_fechaalta']));    
            //  PRL
                $datosParseados[$i]['col16'] = ($datosComunidad[$x]['prl_contratado'] == true ? 'S' : 'N');
                $datosParseados[$i]['col17'] = $datosComunidad[$x]['prl_precio'];
                $datosParseados[$i]['col17'] = $datosComunidad[$x]['prl_preciocomunidad'];
                $datosParseados[$i]['col18'] = date('d/m/Y', strtotime($datosComunidad[$x]['prl_fechaalta']));
    
            //  instalaciones
                $datosParseados[$i]['col19'] = ($datosComunidad[$x]['instalaciones_contratado'] == true ? 'S' : 'N');
                $datosParseados[$i]['col20'] = $datosComunidad[$x]['instalaciones_precio'];
                $datosParseados[$i]['col21'] = $datosComunidad[$x]['instalaciones_preciocomunidad'];
                $datosParseados[$i]['col22'] = date('d/m/Y', strtotime($datosComunidad[$x]['instalaciones_fechaalta']));
    
            //  Certificados digitales
                $datosParseados[$i]['col23'] = ($datosComunidad[$x]['certificados digitales_contratado'] == true ? 'S' : 'N');
                $datosParseados[$i]['col24'] = $datosComunidad[$x]['certificados digitales_precio'];
                $datosParseados[$i]['col25'] = $datosComunidad[$x]['certificados digitales_preciocomunidad'];            
                $datosParseados[$i]['col26'] = date('d/m/Y', strtotime($datosComunidad[$x]['certificados digitales_fechaalta']));
                $datosParseados[$i]['col27'] = $datosComunidad[$x]['estado'];

        }

        return $datosParseados;
    }

    /**
     * Recupera el documento identificativo de un administrador
     */
    public function DocumentoIdentificativo($administradorId)
    {
        $administradorId = DatabaseCore::PrepareDBString($administradorId);
        $sql = "select * from " . $this->tablaDocumento . " where usuarioid = " . $administradorId . "";
        return $this->query($sql);
    }

    /**
     * Asocia el documento identificativo a un administrador
     */
    public function InsertDocumentoIdentificativo($administradorId, $documentFrontalId, $documentRearId)
    {
        //  Almacenamos el id de usuario en md5
        $administradorId = DatabaseCore::PrepareDBString($administradorId);
        //  Almacenamos el id de documento frontal en md5
        $documentFrontalId = DatabaseCore::PrepareDBString($documentFrontalId);
        //  Almacenamos el id de documento trasero en md5
        $documentRearId = DataBaseCore::PrepareDBString($documentRearId);

        $sql = "insert into " . $this->tablaDocumento . "(usuarioid, frontid, rearid, created, usercreate) values (";
        $sql .= $administradorId . ", ";
        $sql .= $documentFrontalId . ", ";
        $sql .= $documentRearId . ", ";
        $sql .= "now(), " . $this->getLoggedUserId() . ")";
        $this->queryRaw($sql);
    }

    public function UpdateDocumentoIdentificativo($administradorId, $documentFrontalId, $documentRearId)
    {
        //  Almacenamos el id de usuario en md5
        $administradorId = DatabaseCore::PrepareDBString($administradorId);
        //  Almacenamos el id de documento frontal en md5
        $documentFrontalId = DatabaseCore::PrepareDBString($documentFrontalId);
        //  Almacenamos el id de documento trasero en md5
        $documentRearId = DataBaseCore::PrepareDBString($documentRearId);        
        $sql = "update " . $this->tablaDocumento . " set frontid = $documentFrontalId, rearid = $documentRearId, updated = now() ";
        $sql .= "where usuarioid = $administradorId" ;
        $this->queryRaw($sql);
    }

    /**
     * Administradores que tienen contratado DPD para alguna comunidad
     */
    public function AdministradoresDPD()
    {
        $sql = "SELECT 
                    u.nombre, u.cif, u.direccion, u.localidad, p.nombre as provincia, u.telefono, u.email
                FROM
                    comunidadservicioscontratados csc, comunidad c, usuario u, provincia p
                WHERE
                    c.id = csc.idcomunidad
                    AND csc.idservicio = 2 AND csc.contratado = 1
                    AND u.id = c.usuarioid AND c.estado IN ('A' , 'P')
                    and p.id = u.provinciaid
                    and u.estado = 'A'
                GROUP BY u.id
                ORDER BY u.nombre ASC";
        return $this->query($sql);
    }

}