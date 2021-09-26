<?php

namespace Fincatech\Model;

use Fincatech\Entity\Dpd;

class DpdModel extends \HappySoftware\Model\Model{

    private $entidad = 'Dpd';

    private $tablasSchema = array("dpd","comunidad");

    /**
     * @var \Fincatech\Entity\Dpd
     */
    public $dpd;

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