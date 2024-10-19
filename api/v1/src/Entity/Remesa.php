<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;
use HappySoftware\Controller\HelperController;

class Remesa extends EntityHelper{

    private $tableName = 'remesa';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = null;
    public $orderType = null;
    
    //  ID
    private int $_id;
    //  Referencia única de la remesa
    private string $_referencia;
    //  Fecha de creación
    private string $_creationDate;
    //  Nombre del crediticio
    private string $_creditorName;
    //  IBAN donde se va a domiciliar el cobro
    private string $_creditorAccountIBAN;
    //  BIC del IBAN donde se va a domiciliar el cobro
    private string $_creditorAgentBIC;
    // Creditor ID
    private string $_creditorId;
    //  Nombre del Administrador
    private string $_customerName;
    //  ID del administrador
    private int $_customerId;
    //  Total 
    private float $_totalAmount;
    //  Fecha de creación
    private string $_created;    
    //  Usuario de creación
    private int $_usercreate;

    /**
     * ID interno de la remesa
     */
    public function Id(){return $this->_id;}
    public function SetId($value){
        $this->_id = (int)$value;
        return $this;
    }

    public function Referencia(){return $this->_referencia;}
    public function SetReferencia($value){
        $this->_referencia = $value;
        return $this;
    }

    public function CreationDate(){return $this->_creationDate;}
    public function SetCreationDate($value){
        $this->_creationDate = $value;
        return $this;
    }

    public function CreditorName(){return $this->_creditorName;}
    public function SetCreditorName($value){
        $this->_creditorName = $value;
        return $this;
    }

    public function CreditorAccountIBAN(){return HelperController::NormalizeIBAN($this->_creditorAccountIBAN);}
    public function SetCreditorAccountIBAN($value){
        $this->_creditorAccountIBAN = HelperController::NormalizeIBAN($value);
        return $this;
    }

    public function CreditorAgentBIC(){return $this->_creditorAgentBIC;}
    public function SetCreditorAgentBIC($value){
        $this->_creditorAgentBIC = $value;
        return $this;
    }

    //  Creditor Id
    public function CreditorId(){return $this->_creditorId;}
    public function SetCreditorId($value){
        $this->_creditorId = $value;
        return $this;
    }
    //  Nombre del Administrador
    public function CustomerName(){ return $this->_customerName;}
    public function SetCustomerName($value){
        $this->_customerName = $value;
        return $this;
    }

    /**
     * Id del administrador
     */
    public function CustomerId(){ return $this->_customerId;}
    public function SetCustomerId($value){
        $this->_customerId = $value;
        return $this;
    }

    //  Total 
    public function TotalAmount(){ return $this->_totalAmount; }
    public function SetTotalAmount($value){
        $this->_totalAmount = $value;
        return $this;
    }

    public function Created(){return $this->_created;}
    public function SetCreated($value){
        $this->_created = $value;
        return $this;
    }

    public function UserCreate(){return $this->_usercreate;}
    public function SetUserCreate(int $value){
        $this->_usercreate = $value;
        return $this;        
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
        //  Entidad remesadetalle
        $this->relations[] = $this->addRelation([
            'table' => 'remesadetalle',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'id',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'idremesa',
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