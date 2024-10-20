<?php

namespace Fincatech\Model;

class CronModel extends \HappySoftware\Model\Model{

    private $entidad = 'Cron';

    private $tablasSchema = array('cron');

    /**
     * @var \Fincatech\Entity\Cron
     */
    public $cron;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    /** Listado de empresas de alta en el sistema */
    public function ListEmpresas()
    {
        $sql = "
            SELECT 
                e.id, e.email, e.razonsocial, e.cif, e.personacontacto
            FROM
                empresa e,
                usuario u
            WHERE
                u.id = e.idusuario";
        return $this->query($sql);
    }

    public function MarcarRequerimientoPendiente($idRequerimiento, $entidad)
    {
        //  Estado: No adjuntado (1) Tabla documentoestado
        $sql = "update " . $entidad . " set idestado = 1, idfichero = null  where id = $idRequerimiento";
        $this->queryRaw($sql);
    }

    /** Documentos caducados de empresas */
    public function DocumentosEmpresaCAECaducados($idEmpresa)
    {
        $sql = "
            SELECT 
                e.id as idempresa,
                e.cif as cifempresa,
                e.razonsocial as empresa,
                c.nombre as comunidad,
                e.telefono as telefonoempresa,
                e.personacontacto, e.email,
                e.idusuario,
                er.idrequerimiento,
                er.id as id,
                er.idestado,
                er.fechacaducidad,
                r.nombre as nombrerequerimiento
            FROM
                empresa e,
                empresarequerimiento er
                left join comunidad c on c.id = er.idcomunidad,    
                requerimiento r,
                usuario u
            WHERE
                e.id = $idEmpresa
                and u.id = e.idusuario
                and er.idempresa = u.id
                and r.id = er.idrequerimiento
                and er.fechacaducidad < now() 
                and er.idestado = 6    
        ";

        $requerimientos = $this->query($sql);
        
        //  Cargamos en el objeto tanto el número de requerimientos caducados como los requerimientos si los hubiera
        $documentos = [];
        $documentos['total'] = count($requerimientos);
        $documentos['requerimientos'] = $requerimientos;        

        return $documentos;
    }

    /** Documentos caducados de empleados de empresa
     * @param int $idEmpresa ID de la empresa
     * @return Array Datos de la consulta
     */
    public function DocumentosEmpleadoEmpresaCaducados($idEmpresa){
        
        $totalCaducados = 0;

        $sql = "
            SELECT 
                e.id as idempleado,
                e.nombre as empleado,
                e.email as emailempleado,
                emp.id as idempresa,
                emp.razonsocial as razonsocial,
                emp.personacontacto as personacontacto,
                emp.email as emailempresa
            FROM
                empleado e,
                empleadocomunidad ec,
                empleadoempresa ee,
                empresa emp
            where 
                ee.idempleado = ec.idempleado
                and emp.idusuario = ee.idempresa
                and e.id = ee.idempleado
                and e.estado = 'A'
                and emp.id = $idEmpresa
            group by e.id        
        ";

        $empleados = $this->query($sql);

        //  Por cada uno de los empleados comprobamos los requerimientos que pueda tener caducados
        for($x = 0; $x < count($empleados); $x++)
        {
            $sql = "
            SELECT 
                er.id,
                er.idrequerimiento,
                er.idempleado,
                er.fechacaducidad,
                er.idfichero,
                r.nombre AS requerimiento
            FROM
                empleadorequerimiento er,
                requerimiento r
            WHERE
                r.id = er.idrequerimiento
                and er.fechacaducidad < now()
                and er.idestado = 6
                and er.idempleado = " . $empleados[$x]['idempleado'];

            $documentos = $this->query($sql);
            $empleados[$x]['requerimientos'] = $documentos;
            $totalCaducados += count($documentos);
        }
        $documentos = [];
        $documentos['requerimientos'] = $empleados;
        $documentos['total'] = $totalCaducados;

        return $documentos;

    }

    public function ListMensajesSinInforme()
    {
        $sql = "select * from emailscertificados where filename is null and created > '2024-05-20'";
        return $this->query($sql);
    }

    /**
     * Recupera aquellas empresas que están registradas en el sistema pero no se les envió el e-mail de alta en la plataforma
     */
    public function ListEmpresasSinEnvioEmailAlta()
    {
        $sql = "
            SELECT e.id, e.usercreate, e.idusuario, e.razonsocial, e.personacontacto, e.email, e.created
            FROM empresa e
            WHERE NOT EXISTS (
                SELECT 1
                FROM mensaje m
                WHERE m.email = e.email
                AND m.subject = 'Fincatech - Alta en la plataforma'
            )
            and e.created >= '2024-01-01'
        ";
        return $this->query($sql);
    }

    /**
     * Devuelve aquellas empresas que han sido dadas de alta pero nunca han accedido al sistema
     */
    public function ListEmpresasPendientesAcceso()
    {
        $sql = "SELECT 
                    e.id idempresa,
                    e.razonsocial,
                    e.idusuario,
                    e.email emailempresa,
                    u.created fecharegistro,
                    u.email emailusuario,
                    u.emailcontacto emailcontactousuario
                FROM
                    empresa e, usuario u
                where 
                    u.id = e.idusuario
                    and u.lastlogin is null
                    and u.estado = 'a'";
        return $this->query($sql);
    }

    public function MensajeEnviadoRegistroEmpresa($emailEmpresa)
    {
        $sql = "select id, numeroenvio from mensaje where email = '$emailEmpresa' and subject = 'Fincatech - Alta en la plataforma'  order by id desc limit 1";
        return $this->query($sql);
    }


}