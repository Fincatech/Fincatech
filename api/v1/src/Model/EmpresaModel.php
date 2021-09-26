<?php

namespace Fincatech\Model;

use Fincatech\Entity\Empresa;

class EmpresaModel extends \HappySoftware\Model\Model{

    private $entidad = 'Empresa';

    private $tablasSchema = array("Empresa");

    /**
     * @var \Fincatech\Entity\Empresa
     */
    public $empresa;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

}