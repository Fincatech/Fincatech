<?php

namespace Fincatech\Model;

use Fincatech\Entity\NotasInformativas;

class NotasInformativasModel extends \HappySoftware\Model\Model{

    private $entidad = 'NotasInformativas';

    private $tablasSchema = array("notasinformativas");

    /**
     * @var \Fincatech\Entity\NotasInformativas
     */
    public $notasinformativas;

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