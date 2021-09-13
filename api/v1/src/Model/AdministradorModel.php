<?php

namespace Fincatech\Model;

use Fincatech\Entity\Usuario\Usuario;

class AdministradorModel extends \HappySoftware\Model\Model{

    private $entidad = 'Usuario';

    private $tablasSchema = array("usuario, usuarioRol");

    public $administrador;

    public function __construct($params = null)
    {
        //  Inicializamos la entidad
        $this->InitEntity( $this->entidad );

        //  Inicializamos el modelo
        $this->InitModel($this->entidad, $params, $this->tablasSchema);

    }

}