<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;
use HappySoftware\Controller\HelperController;

class RemesaDetalle extends EntityHelper{

    private $tableName = 'remesadetalle';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = null;
    public $orderType = null;
    
    //  Propiedades de la entidad
    //  ID
    private int $_id;
    //  Id de la remeesa
    private int $_idRemesa;
    //  Id de la factura asociada
    private int $_invoiceId;
    //  Concepto
    private string $_descripcion;
    //  Cantidad
    private float $_amount;
    //  Customer Name
    private string $_customerName;
    //  Customer BIC
    private string $_customerBIC;
    //  Customer IBAN
    private string $_customerIBAN;
    //  UniqueId
    private string $_uniqId;
    //  Estado
    private string $_estado = 'C'; // Por defecto se da como cobrada
    //  Fecha devolución
    private string $_dateReturned;
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
    public function SetId($value){
        $this->_id = (int)$value; return $this;
    }

    /**
     * ID de la remesa
     */
    public function IdRemesa(){return $this->_idRemesa;}
    public function SetIdRemesa($value){
        $this->_idRemesa = (int)$value; 
        return $this;
    }

    /**
     * ID de la factura
     */
    public function InvoiceId(){return $this->_invoiceId;}
    public function SetInvoiceId(int $value)
    {
        $this->_invoiceId = $value; 
        return $this;
    }

    /**
     * Descripción del concepto
     */
    public function Description(){return $this->_descripcion;}
    public function SetDescription($value){
        $this->_descripcion = $value; return $this;
    }

    /**
     * Amount
     */
    public function Amount(){ return $this->_amount;}
    public function SetAmount($value){
        $this->_amount = $value; return $this;
    }

    /**
     * UniqueID
     */
    public function UniqueId(){ return $this->_uniqId;}
    public function SetUniqueId($value){       
        $this->_uniqId = $value; return $this;
    }

    /**
     * Customer Name
     */
    public function CustomerName(){return $this->_customerName;}
    public function SetCustomerName($value){
        $this->_customerName = $value; return $this;
    }
    /**
     * Customer BIC
     */
    public function CustomerBIC(){ return $this->_customerBIC;}
    public function SetCustomerBIC($value){
        $this->_customerBIC = $value; return $this;
    }
    /**
     * Customer IBAN
     */
    public function CustomerIBAN(){ return $this->_customerIBAN;}
    public function SetCustomerIBAN($value){
        $this->_customerIBAN = $value; return $this;
    }

    /**
     * Estado del recibo
     */
    public function Estado(){ return $this->_estado;}
    public function SetEstado($value){
        $this->_estado = $value;
        return $this;
    }

    /**
     * Fecha de devolución del recibo
     */
    public function DateReturned(){ return $this->_dateReturned;}
    public function SetDateReturned($value){
        $this->_dateReturned = $value;
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