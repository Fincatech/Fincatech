<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;

class InformeValoracionSeguimiento extends EntityHelper{

    private $tableName = 'InformeValoracionSeguimiento';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;

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
        // Entidad Ficheros comunes
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

        // //  Entidad comunidad
        // $this->relations[] = $this->addRelation([
        //     'table' => 'comunidad',
        //     //  Columna de la entidad que se está relacionando con la entidad principal
        //     'sourceColumn' =>'usuarioId',
        //     //  Columna de la entidad principal con la que se va a relacionar
        //     'targetColumn' => 'id',
        //     'fieldType' => 'int',
        //     'deleteMode' => DELETE_FISICO,
        //     //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
        //     'relationType' => RELACION_OUTSIDE
        // ]);

    }

}