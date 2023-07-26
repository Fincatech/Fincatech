<?php

namespace Fincatech\Model;

use Fincatech\Entity\Stats;

class StatsModel extends \HappySoftware\Model\Model{

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
        return $this->getRepositorio()->selectCount('comunidad');
    }

    public function TotalAdministradores()
    {
        return $this->getRepositorio()->selectCount('usuario', 'rolid','=',5);
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

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}