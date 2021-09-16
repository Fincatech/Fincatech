<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;

class Estado extends EntityHelper{

    private $tableName = 'Estado';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = "nombre";
    public $orderType = ORDER_BY_ASC;

    /**
     * @var bool
     * Indica si se puede devolver el schema
     */
    public $canReturnSchema;

    /** @var Relation */
    private $relations = [];

    public function __construct()
    {

        //  Inicializamos el entity helper
        $this->InitEntity();

        //  Establecemos las relaciones
        $this->setRelations();

    }

    /** Establece las relaciones de entidades con esta entidad */
    private function setRelations()
    {       
    }

}