<?php

namespace Fincatech\Model;

use Fincatech\Entity\InformeValoracionSeguimiento;

class InformeValoracionSeguimientoModel extends \HappySoftware\Model\Model{

    private $entidad = 'InformeValoracionSeguimiento';

    private $tablasSchema = array("InformeValoracionSeguimiento", "ficheroscomunes");

    /**
     * @var \Fincatech\Entity\InformeValoracionSeguimiento
     */
    public $usuario;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}