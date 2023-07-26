<?php

namespace Fincatech\Model;

use Fincatech\Entity\Rgpdempleado;

class RgpdempleadoModel extends \HappySoftware\Model\Model{

    private $entidad = 'Rgpdempleado';

    private $tablasSchema = array("usuario, usuarioRol");

    /**
     * @var \Fincatech\Entity\Rgpdempleado
     */
    public $rgpdEmpleado;

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