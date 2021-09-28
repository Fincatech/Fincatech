<?php

namespace Fincatech\Model;

use Fincatech\Entity\Tipopuestoempleado;

class TipopuestoempleadoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Tipopuestoempleado';

    private $tablasSchema = array("Tipopuestoempleado, usuarioRol");

    /**
     * @var \Fincatech\Entity\Tipopuestoempleado
     */
    public $tipopuestoempleado;

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