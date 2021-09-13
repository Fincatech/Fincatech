<?php

namespace Fincatech\Model;

use Fincatech\Entity\Provincia;

class ProvinciaModel extends \HappySoftware\Model\Model{

    private $entidad = 'Provincia';

    private $tablasSchema = array("Provincia");

    /**
     * @var \Fincatech\Entity\Provincia
     */
    public $provincia;

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