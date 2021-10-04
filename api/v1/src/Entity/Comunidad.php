<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;

class Comunidad extends EntityHelper{

    private $tableName = 'comunidad';
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

        //  Entidad Usuario
        $this->relations[] = $this->addRelation([
            'table' => 'usuario',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'usuarioId',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'readOnly' => true,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);

        //  Empresas asociadas a la comunidad
        $this->relations[] = $this->addRelation([
            'table' => 'view_empresascomunidad',
            'alias' => 'empresascomunidad',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idcomunidad',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'readOnly' => true,
            'canReturnSchema' => false,
            'deleteOnCascade' => false,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_OUTSIDE
        ]);

        //  Empleados
        // $this->relations[] = $this->addRelation([
        //     'table' => 'view_documentoscomunidad',
        //     'alias' => 'documentacioncomunidad',
        //     //  Columna de la entidad que se está relacionando con la entidad principal
        //     'sourceColumn' =>'idcomunidad',
        //     //  Columna de la entidad principal con la que se va a relacionar
        //     'targetColumn' => '@IDEMPRESAREQUERIMIENTO:',
        //     'fieldType' => 'int',
        //     'readOnly' => true,
        //     'canReturnSchema' => false,
        //     'deleteOnCascade' => false,
        //     //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
        //     'relationType' => RELACION_OUTSIDE
        // ]);

        // //  Servicios contratados
        // $this->relations[] = $this->addRelation([
        //     'table' => 'comunidadservicioscontratados',
        //     'alias' => 'servicioscontratados',
        //     //  Columna de la entidad que se está relacionando con la entidad principal
        //     'sourceColumn' =>'idcomunidad',
        //     //  Columna de la entidad principal con la que se va a relacionar
        //     'targetColumn' => 'id',
        //     'fieldType' => 'int',
        //     'readOnly' => true,
        //     'canReturnSchema' => false,
        //     'deleteOnCascade' => false,
        //     //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
        //     'relationType' => RELACION_OUTSIDE
        // ]);

        //  Entidad usuario_roles
        $this->relations[] = $this->addRelation([
            'table' => 'spa',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idspa',
            //  Columna de la entidad principal con la que se va a relacionar
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