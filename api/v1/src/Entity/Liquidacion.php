<?php

namespace Fincatech\Entity;

use Fincatech\Entity\LiquidacionDetalle;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;
use HappySoftware\Controller\HelperController;

class Liquidacion extends EntityHelper{

    private $tableName = 'liquidacion';
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
    //  Nombre del administrador
    private string $_administrador = '';
    //  Fecha desde
    private string $_dateFrom;
    //  Fecha hasta
    private string $_dateTo;
    //  Total impuestos incluidos
    private float $_total_taxes_inc;
    //  Total impuestos excluidos
    private float $_total_taxes_exc;
    // % de impuesto aplicado
    private float $_tax_rate;
    //  Total calculado de ingresos a cuenta
    private float $_total_a_cuenta;
    //  Estado de la liquidacion
    private string $_estado = 'P';
    //  Referencia de la liquidacion
    private string $_referencia = '';
    //  Detalle de la liquidación
    private $_detalle = [];
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
     * Nombre del administrador
     */
    public function Administrador(){return $this->_administrador;}
    public function SetAdministrador(string $value){
        $this->_administrador = $value;
        return $this;
    } 

    /**
     * Fecha desde
     */
    public function DateFrom(){return $this->_dateFrom;}
    public function SetDateFrom($value){
        $this->_dateFrom = $value;
        return $this;
    }

    /**
     * Fecha hasta
     */
    public function DateTo(){return $this->_dateTo;}
    public function SetDateTo($value){
        $this->_dateTo = $value;
        return $this;
    }

    /**
     * Total Impuestos Incluidos
     */
    public function TotalTaxesInc(){ return $this->_total_taxes_inc;}
    public function SetTotalTaxesInc($value){
        $this->_total_taxes_inc = $value;
        return $this;
    }

    /**
     * Total Impuestos Excluidos
     */
    public function TotalTaxesExc(){return $this->_total_taxes_exc;}
    public function SetTotalTaxesExc($value){
        $this->_total_taxes_exc = $value;
        return $this;
    }

    /**
     * % Impuesto
     */
    public function TaxRate(){return $this->_tax_rate;}
    public function SetTaxRate($value){
        $this->_tax_rate = $value;
        return $this;
    }

    /**
     * Total a cuenta
     */
    public function TotalACuenta(){return $this->_total_a_cuenta;}
    public function SetTotalACuenta($value){
        $this->_total_a_cuenta = $value;
        return $this;
    }

    /**
     * Estado
     */
    public function Estado(){ return $this->_estado;}
    public function SetEstado($value){
        $this->_estado = $value;
        return $this;
    }

    /**
     * Referencia de la liquidación
     */
    public function Referencia(){return $this->_referencia;}
    public function SetReferencia($value){
        $this->_referencia = $value;
        return $this;
    }

    public function Detalle(){return $this->_detalle;}
    public function SetDetalle(LiquidacionDetalle $value){
        $this->_detalle[] = $value;
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
            'table' => 'liquidaciondetalle',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'id',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'idliquidacion',
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