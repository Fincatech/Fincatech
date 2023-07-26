<?php

namespace Fincatech\Model;

use Fincatech\Entity\Requerimientodescargas;

class RequerimientodescargasModel extends \HappySoftware\Model\Model{

    private $entidad = 'Usuario';

    private $tablasSchema = array("usuario", "usuarioRol");

    /**
     * @var \Fincatech\Entity\Requerimientodescargas
     */
    public $requerimientodescargas;

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