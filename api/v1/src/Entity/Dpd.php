<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;

class Dpd extends EntityHelper{

    private $tableName = 'dpd';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = true;
    public $orderBy = 'created';
    public $orderType = 'DESC';
    
    /**
     * @var bool
     * Indica si se puede devolver el schema
     */
    public $canReturnSchema = false;

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

        //  Entidad comunidad
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

        //  Entidad Ficheros
        $this->relations[] = $this->addRelation([
            'table' => 'ficheroscomunes',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idfichero',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'canReturnSchema' => false,
            'readOnly' => true,
            //  Indica si se debe eliminar el registro relacionado o no
            'deleteOnCascade' => true,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);

        //  Usuario
        $this->relations[] = $this->addRelation([
            'table' => 'usuario',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'usercreate',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'canReturnSchema' => false,
            'readOnly' => true,
            //  Indica si se debe eliminar el registro relacionado o no
            'deleteOnCascade' => true,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);        

        // //  Entidad fichero
        // $this->relations[] = $this->addRelation([
        //     'table' => 'ficheroscomunes',
        //     //  Columna de la entidad que se está relacionando con la entidad principal
        //     'sourceColumn' =>'id',
        //     //  Columna de la entidad principal con la que se va a relacionar
        //     'targetColumn' => 'idfichero',
        //     'fieldType' => 'int',
        //     'readOnly' => true,
        //     'canReturnSchema' => false,
        //     'deleteMode' => DELETE_FISICO,
        //     //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
        //     'relationType' => RELACION_OUTSIDE
        // ]);

        // //  Entidad administrador
        // $this->relations[] = $this->addRelation([
        //     'table' => 'usuario',
        //     //  Columna de la entidad que se está relacionando con la entidad principal
        //     'sourceColumn' =>'id',
        //     //  Columna de la entidad principal con la que se va a relacionar
        //     'targetColumn' => 'idadministrador',
        //     'fieldType' => 'int',
        //     'readOnly' => true,
        //     'canReturnSchema' => false,
        //     'deleteMode' => DELETE_FISICO,
        //     //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
        //     'relationType' => RELACION_OUTSIDE
        // ]);

    }

}