<?php

namespace Fincatech\Model;

use Fincatech\Entity\Estado;

class EstadoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Estado';

    private $tablasSchema = array("Estado");

    /**
     * @var \Fincatech\Entity\Estado
     */
    public $estado;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

}