<?php

namespace Fincatech\Model;

use Fincatech\Entity\Stats;

class StatsModel extends \HappySoftware\Model\Model{

    public int $totalAdministradores = 0;
    public int $totalNotasInformativas = 0;
    public int $totalInformesSeguimiento = 0;

    private $entidad = 'Spa';

    private $tablasSchema = array("spa", "usuario", "usuarioRol");

    /**
     * @var \Fincatech\Entity\Stats
     */
    public $Stats;

    private $repositorio;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->repositorio = $this->getRepositorio();
        // $this->InitEntity( $this->entidad );
        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    public function TotalComunidades()
    {
        return $this->getRepositorio()->selectCount('comunidad', 'estado', '=', "'A'");
    }

    public function TotalAdministradores()
    {
        return $this->getRepositorio()->selectCount('usuario', "idadministrador is null and estado = 'A' and rolid", '=', 5);
    }

    public function TotalServiciosFacturados()
    {
        return 10;
    }

    public function TotalEmpresas()
    {
        return $this->getRepositorio()->selectCount('empresa');
    }

    public function TotalEmailsCertificados()
    {
        return $this->getRepositorio()->selectCount('emailscertificados', 'filename', 'is not', 'null');
    }

    public function TotalEmailsEnviados()
    {
        return $this->getRepositorio()->selectCount('mensaje');
    }

    /**
     * Total servicios contratados por comunidades activas en el sistema
     */
    public function ServiciosContratadosComunidades()
    {
        $sql = "
            SELECT 
                sum(case when csc.idservicio = 1 then 1 else 0 end) as totalcae,
                sum(case when csc.idservicio = 2 then 1 else 0 end) as totaldpd,
                sum(case when csc.idservicio = 3 then 1 else 0 end) as totaldoccae,
                sum(case when csc.idservicio = 4 then 1 else 0 end) as totalinstalaciones,
                sum(case when csc.idservicio = 5 then 1 else 0 end) as totalcertificadosdigitales    
            FROM
                comunidadservicioscontratados csc, comunidad c 
            where 
            csc.contratado = 1
            and c.id = csc.idcomunidad and c.estado = 'A' ";
        return mysqli_fetch_assoc($this->getRepositorio()->queryRaw($sql));
    }

    public function Dashboard()
    {
        $sql = "select * from
            (select count(*) administradores from usuario where rolid = 5 and estado = 'A') as administradores,
            (select count(*) informevaloracion from informevaloracionseguimiento) as informeValoracionSeguimiento,
            (select count(*) notasinformativas from notasinformativas) as notasInformativas,
            (select count(*) consultasdpd from dpd) as consultas";
        $result = $this->query($sql);
        return $result;
    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}