<?php

namespace Fincatech\Model;

use Fincatech\Entity\Comunidad;

use Fincatech\Model\Requerimiento;

class ComunidadModel extends \HappySoftware\Model\Model{

    private $entidad = 'Comunidad';

    private $tablasSchema = array("comunidad");

    /**
     * @var \Fincatech\Entity\Usuario\Comunidad
     */
    public $comunidad;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

    //  Recuperamos el listado de las comunidades

    // /** Recupera todos los registros */
    // public function List($params = null)
    // {
    //     return parent::List($params);
    // }

}