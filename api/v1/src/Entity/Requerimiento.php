<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;

class Requerimiento extends EntityHelper{

    private $tableName = 'requerimientos';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = "orden";
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

        //  Fichero
        $this->relations[] = $this->addRelation([
            'table' => 'ficheroscomunes',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idfichero',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'readOnly' => true,
            'canReturnSchema' => false,
            'deleteMode' => DELETE_FISICO,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);

        //  Comunidad asociada
        $this->relations[] = $this->addRelation([
            'table' => 'comunidad',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'id',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'idcomunidad',
            'fieldType' => 'int',
            'readOnly' => true,
            'canReturnSchema' => false,
            'deleteMode' => DELETE_FISICO,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_OUTSIDE
        ]);

        //  Tipo de requerimiento
        $this->relations[] = $this->addRelation([
            'table' => 'requerimientotipo',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idrequerimientotipo',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'readOnly' => true,
            'canReturnSchema' => false,
            'deleteMode' => DELETE_FISICO,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);        

    }

}