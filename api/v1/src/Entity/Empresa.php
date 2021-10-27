<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;

class Empresa extends EntityHelper{

    private $tableName = 'Empresa';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = "razonsocial";
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

            //  Entidad provincia
        $this->relations[] = $this->addRelation([
            'table' => 'empresatipo',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idtipoempresa',
            //  Columna de la entidad con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'canReturnSchema' => false,
            'readOnly' => true,
            //  Indica si se debe eliminar el registro relacionado o no
            'deleteOnCascade' => false,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);

        $this->relations[] = $this->addRelation([
            'table' => 'usuario',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idusuario',
            //  Columna de la entidad con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'canReturnSchema' => false,
            'readOnly' => true,
            //  Indica si se debe eliminar el registro relacionado o no
            'deleteOnCascade' => false,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);


    }

}