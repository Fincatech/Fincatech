<?php

namespace Fincatech\Model;

use Fincatech\Entity\Requerimientotipo;

class RequerimientotipoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Requerimientotipo';

    private $tablasSchema = array("Requerimientotipo");

    /**
     * @var \Fincatech\Entity\Requerimientotipo
     */
    public $requerimientotipo;

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