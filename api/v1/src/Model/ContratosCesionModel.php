<?php

namespace Fincatech\Model;

use Fincatech\Entity\ContratosCesion;

class ContratosCesionModel extends \HappySoftware\Model\Model{

    private $entidad = 'ContratosCesion';

    private $tablasSchema = array("ContratosCesion");

    /**
     * @var \Fincatech\Entity\ContratosCesion
     */
    public $contratosCesion;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    // /** Recupera todos los registros */
    public function List($params = null, $useLoggedUser = false)
    {
        return parent::List($params, $useLoggedUser);
    }

}