<?php

namespace Fincatech\Model;

use Fincatech\Entity\Usuariorol;

class RolModel extends \HappySoftware\Model\Model{

    private $entidad = 'Rol';

    private $tablasSchema = array("Rol");

    /**
     * @var \Fincatech\Entity\Rol
     */
    public $rol;

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