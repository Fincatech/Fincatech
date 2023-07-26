<?php

namespace Fincatech\Model;

use Fincatech\Entity\Requerimiento;

class RequerimientoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Requerimiento';

    private $tablasSchema = array("requerimiento", "comunidad", "comunidadrequerimientos", "ficheroscomunes");

    /**
     * @var \Fincatech\Entity\Requerimiento
     */
    public $requerimiento;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    // TODO: Falta la implementación
    public function ListRequerimientosComunidad($comunidadId)
    {

        //  Recuperamos 
        $sql = "
            SELECT 
                req.*
            FROM
                requerimientos req
                LEFT JOIN comunidadrequerimientos comreq on comreq.idrequerimiento = req.id and comreq.idcomunidad = $comunidadId
            WHERE
                req.tipo = 'COM'";

    }

    /** Devuelve un listado de entidad requerimiento por idtipo */
    public function ListRequerimientoByIdTipo($idTipo)
    {
        $data = $this->List(null, false);
        $data = $this->filterResults($data, 'Requerimiento', 'idrequerimientotipo', $idTipo);
        return $data;
    }

    public function GetRequerimientosEmpresa($idEmpresa)
    {
        $this->InitController('Documental');
        $tipoEmpresa = $this->DocumentalController->GetTipoContratista($idEmpresa);
        if($tipoEmpresa == 1)
        {
            //  Empresa externa
            $vista = 'view_documentoscaeempresa';
        }else{
            //  Autónomo
            $vista = 'view_documentoscaeautonomo';
        }
        $sql = "SELECT * FROM $vista where @p1:=" . $idEmpresa; 
        return $this->query($sql);

    }

    /** Devuelve los requerimientos caducados de empleados */
    public function GetRequerimientosCaducadosEmpleado()
    {
        $sql = "
            SELECT 
            e.nombre,
            tpe.nombre puesto,
            r.nombre requerimiento, date_format(timestamp(er.fechacaducidad),'%d-%m-%Y') fecha,
            date_format(timestamp(er.created),'%d-%m-%Y') fecha_subida,
            emp.idempresa, em.razonsocial empresa, em.personacontacto personacontacto, em.email email
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
        ORDER BY idempresa ASC, fecha ASC";
        return  $this->query($sql);
    }

    /** Devuelve los requerimientos caducados de CAE para una empresa */
    public function GetRequerimientosCaducadosEmpresa()
    {
        $sql = "
            SELECT 
                r.nombre requerimiento,
                em.id idempresa,
                date_format(timestamp(er.fechacaducidad),'%d-%m-%Y') fecha,
                em.razonsocial empresa, em.personacontacto personacontacto, em.email email
            FROM
                empresarequerimiento er
                left join comunidad c on c.id = er.idcomunidad,
                requerimiento r,
                empresa em
            WHERE
                DATE(er.fechacaducidad) <= DATE(NOW())
                and r.id = er.idrequerimiento
                and em.idusuario = er.idempresa
            ORDER BY er.idempresa ASC, r.nombre ASC ";
        return  $this->query($sql);
    }

    /** Recupera aquellas empresas que aún asignadas a comunidad no tienen empleados asignados a la comunidad */
    public function GetEmpresasSinEmpleadoAsignadoAComunidad()
    {
        $sql = "
        select A1.*, e.razonsocial empresa, e.email, e.personacontacto, if(A2.total is not null, A2.total, 0) total   from (
            select c.id idcomunidad, c.nombre, c.codigo, ce.idempresa, ce.activa from comunidad c
            left join comunidadempresa ce on ce.idcomunidad = c.id
            where ce.idcomunidad >= 0
        ) A1
        left join (SELECT 
        count(*) as total, ec.idcomunidad, e.id idempresa
        FROM
            empleadocomunidad ec,
            empleadoempresa ee,
            empresa e
        WHERE
            ee.idempleado = ec.idempleado
            and e.idusuario = ee.idempresa
        group by ec.idcomunidad, e.id) A2 on A2.idcomunidad = A1.idcomunidad and A2.idempresa = A1.idempresa,
        empresa e
        where A2.total is null and e.id = A1.idempresa        
        ";
        return $this->query($sql);
    }

    /** Recupera los requerimientos pendientes de subir de una empresa */
    public function GetRequerimientosEmpresaPendientesSubir()
    {
        $sql = "
        select A1.idempresa, A1.requerimiento, A1.razonsocial empresa, A1.personacontacto, A1.email from (
                select e.id, r.nombre requerimiento, e.razonsocial, e.personacontacto, e.email, r.id idrequerimiento, e.idusuario idempresa 
                from empresa e
                left join requerimiento r on r.idrequerimientotipo = 4
                where e.estado = 'A'
            ) A1
            left join empresarequerimiento er on er.idrequerimiento = A1.idrequerimiento and er.idempresa = A1.idempresa
        where 
            er.id is null 
        group by A1.idempresa, A1.idrequerimiento        
        ";
        return $this->query($sql);
    }

    /** Consolida la información de los requerimientos */
    public function ConsolidarInformacionRequerimientos($empleadosNoAsignados, $requerimientosPendientesSubir, $requerimientosEmpleadoCaducados, $requerimientosCaducados)
    {
        $consolidatedData = [];

        //  Mergeamos el array para obtener la información de la empresa
            $empresasRawData = array_merge($empleadosNoAsignados, $requerimientosPendientesSubir, $requerimientosCaducados, $requerimientosEmpleadoCaducados);
            $consolidatedData = $this->obtenerEmpresas($empresasRawData);

        //  Empresas con comunidades asociadas y empleados no asignados
            for($x = 0; $x < count($empleadosNoAsignados); $x++)
            {
                $consolidatedData[$empleadosNoAsignados[$x]['idempresa']]['empleadosnoasignados'][] = $empleadosNoAsignados[$x];
            }
        //  Empresas con requerimientos pendientes de subir
            for($x = 0; $x < count($requerimientosPendientesSubir); $x++)
            {
                $consolidatedData[$requerimientosPendientesSubir[$x]['idempresa']]['pendientesubir'][] = $requerimientosPendientesSubir[$x];
            }        

        //  Empresas con requerimmientos de empleados caducados
            for($x = 0; $x < count($requerimientosEmpleadoCaducados); $x++)
            {
                $consolidatedData[$requerimientosEmpleadoCaducados[$x]['idempresa']]['empleadocaducado'][] = $requerimientosEmpleadoCaducados[$x];
            }    

        //  Empresas con requerimientos caducados
            for($x = 0; $x < count($requerimientosCaducados); $x++)
            {
                $consolidatedData[$requerimientosCaducados[$x]['idempresa']]['empresacaducado'][] = $requerimientosCaducados[$x];
            }

            return array_values($consolidatedData);

    }

    private function obtenerEmpresas($_data)
    {

        $empresasData = array_unique(array_column($_data, 'idempresa'));
        $normalizedData = [];
        $x = 0;
        //  Ahora que hemos obtenido los nombres de las empresas, buscamos el e-mail para cada una de ellas así como la persona de contacto
            foreach($empresasData as $key => $value)
            {

                $idx = array_search($value, array_column($_data, 'idempresa'));
                $_email = $_data[$idx]['email'];
                $_personaContacto = $_data[$idx]['personacontacto'];
                $_empresa = $_data[$idx]['empresa'];
                $_idempresa = $_data[$idx]['idempresa'];

                $normalizedData[$value]['email'] = $_email;
                $normalizedData[$value]['personacontacto'] = $_personaContacto;
                $normalizedData[$value]['empresa'] = $_empresa;
                $normalizedData[$value]['idempresa'] = $_idempresa;
                $normalizedData[$value]['empleadosnoasignados'] = [];
                $normalizedData[$value]['pendientesubir'] = [];
                $normalizedData[$value]['empleadocaducado'] = [];
                $normalizedData[$value]['empresacaducado'] = [];

            }

        //  Devolvemos la estructura del objeto
            return $normalizedData;

    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}