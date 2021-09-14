<?php

namespace Fincatech\Model;

use Fincatech\Entity\Spa;

class SpaModel extends \HappySoftware\Model\Model{

    private $entidad = 'Spa';

    private $tablasSchema = array("spa", "usuario", "usuarioRol");

    /**
     * @var \Fincatech\Entity\Spa
     */
    public $spa;

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