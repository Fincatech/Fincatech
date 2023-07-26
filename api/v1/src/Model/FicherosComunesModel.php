<?php

namespace Fincatech\Model;

use Fincatech\Entity\FicherosComunes;

class FicherosComunesModel extends \HappySoftware\Model\Model{

    private $entidad = 'FicherosComunes';

    private $tablasSchema = array("FicherosComunes");

    /**
     * @var \Fincatech\Entity\FicherosComunes
     */
    public $ficherosComunes;

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