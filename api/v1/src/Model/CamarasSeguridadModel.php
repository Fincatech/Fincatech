<?php

namespace Fincatech\Model;

use Fincatech\Entity\CamaraSeguridad;

class CamarasSeguridadModel extends \HappySoftware\Model\Model{

    private $entidad = 'CamarasSeguridad';

    private $tablasSchema = array("camarasseguridad");

    /**
     * @var \Fincatech\Entity\CamaraSeguridad
     */
    public $camaraseguridad;

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
        return parent::List($params, false);
    }

}