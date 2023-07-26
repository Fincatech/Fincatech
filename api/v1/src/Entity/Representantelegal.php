<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;

class Representantelegal extends EntityHelper{

    private $tableName = 'representantelegal';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = null;
    public $orderType = null;
    
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
        // //  Entidad usuario_roles
        // $this->relations[] = $this->addRelation([
        //     'table' => 'usuarioRol',
        //     //  Columna de la entidad que se está relacionando con la entidad principal
        //     'sourceColumn' =>'rolId',
        //     //  Columna de la entidad principal con la que se va a relacionar
        //     'targetColumn' => 'id',
        //     'fieldType' => 'int',
        //     'canReturnSchema' => false,
        //     'readOnly' => true,
        //     //  Indica si se debe eliminar el registro relacionado o no
        //     'deleteOnCascade' => false,
        //     //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
        //     'relationType' => RELACION_INVERSA
        // ]);

        //  Entidad Imagen delantera
        $this->relations[] = $this->addRelation([
            'table' => 'ficheroscomunes',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'id',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'imagenfrontal',
            'fieldType' => 'int',
            'deleteMode' => DELETE_FISICO,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_OUTSIDE,
            'alias' =>'documentofrontal',
            //  Indica el tipo de relación: One to Many o One To One
            'resultsRelationType' => ONE_TO_ONE
        ]);

        //  Entidad Imagen trasera
        $this->relations[] = $this->addRelation([
            'table' => 'ficheroscomunes',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'id',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'imagentrasera',
            'fieldType' => 'int',
            'deleteMode' => DELETE_FISICO,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_OUTSIDE,
            'resultsRelationType' => ONE_TO_ONE,
            //  Alias de la tabla
            'alias' =>'documentotrasera',
        ]);

    }

}