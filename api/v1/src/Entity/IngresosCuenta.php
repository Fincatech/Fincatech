<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;
use HappySoftware\Controller\HelperController;

class IngresosCuenta extends EntityHelper{

    private $tableName = 'ingresoscuenta';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = null;
    public $orderType = null;
    
    //  Propiedades de la entidad
    //  ID
    private int $_id = -1;
    //  ID del administrador
    private int $_idAdministrador = -1;
    //  ID de la liquidacion
    private int $_idLiquidacion = -1;
    //  Concepto del ingreso a cuenta
    private string $_concepto = '';
    //  Observaciones
    private string $_observaciones = '';
    //  Total 
    private float $_total = 0;
    //  Fecha de ingreso
    private string $_fechaIngreso = '';
    //  Procesado
    private int $_procesado = 0;
    //  ID usuario creación
    private int $_usercreate;
    //  Fecha de creación
    private string $_created;
    //  Fecha de actualización
    private string $_updated;

    /**
     * ID del detalle
     */
    public function Id(){return $this->_id;}
    public function SetId(int|null $value){
        $this->_id = (int)$value; return $this;
    }

    /**
     * ID de administrador
     */
    public function IdAdministrador(){ return $this->_idAdministrador;}
    public function SetIdAdministrador(int $value){
        $this->_idAdministrador = $value;
        return $this;
    }

    /**
     * ID de liquidación
     */
    public function IdLiquidacion(){return $this->_idLiquidacion;}
    public function SetIdLiquidacion(int $value){
        $this->_idLiquidacion = $value;
        return $this;
    }

    /**
     * Concepto
     */
    public function Concepto(){ return $this->_concepto;}
    public function SetConcepto(string $value){
        $this->_concepto = $value;
        return $this;
    }

    /**
     * Observaciones
     */
    public function Observaciones(){ return $this->_observaciones;}
    public function SetObservaciones(string $value){
        $this->_observaciones = $value;
        return $this;
    }

    /**
     * Importe total
     */
    public function Total(){return $this->_total;}
    public function SetTotal(float $value){
        $this->_total = str_replace(',','.',$value);
        return $this;
    }

    /**
     * Procesado ya en liquidaciones
     */
    public function Procesado(){ return $this->_procesado;}
    public function SetProcesado($value){
        $this->_procesado = (int)$value;
        return $this;
    }

    /**
     * Fecha en la que se realizó el ingreso
     */
    public function FechaIngreso(){ return $this->_fechaIngreso;}
    public function SetFechaIngreso($value){
        $this->_fechaIngreso = $value;
        return $this;
    }

    /**
     * Fecha de creación
     */
    public function Created(){return $this->_created;}   
    public function SetCreated($value){
        $this->_created = $value; return $this;
    }

    /**
     * Fecha de actualización
     */
    public function Updated(){ return $this->_updated;}
    public function SetUpdated($value){
        $this->_updated = $value; return $this;
    }

    /**
     * ID usuario creación
    */
    public function UserCreate(){return $this->_usercreate;}
    public function SetUserCreate(int $value){
        $this->_usercreate = $value; return $this;        
    }

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
        //  Entidad Administrador
        $this->relations[] = $this->addRelation([
            'table' => 'usuario',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idadministrador',
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