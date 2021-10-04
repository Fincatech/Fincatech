<?php

namespace Fincatech\Model;

use Fincatech\Entity\Servicios;

class ServiciosModel extends \HappySoftware\Model\Model{

    private $entidad = 'Tiposservicios';

    private $tablasSchema = array("servicios, usuarioRol");

    /**
     * @var \Fincatech\Entity\Servicios
     */
    public $servicios;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    // /** Recupera todos los registros */
    public function List($params = null, $useLoggedUserId = false)
    {
        return parent::List($params, $useLoggedUserId);
    }

}