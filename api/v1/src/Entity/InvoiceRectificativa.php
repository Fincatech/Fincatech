<?php

namespace Fincatech\Entity;

use HappySoftware\Entity\EntityHelper;

use HappySoftware\Entity\DatabaseHelper;
use HappySoftware\Entity\DatabaseHelper\Relations;
use HappySoftware\Controller\HelperController;

class InvoiceRectificativa extends EntityHelper{

    private $tableName = 'invoicerectificativa';
    public $primaryKey = 'id';
    public $tipoEliminacion = DELETE_FISICO;
    public $moveToHistorial = false;
    public $orderBy = null;
    public $orderType = null;
    
    //  Propiedades de la entidad
    //  ID
    private int $_id;
    //  Id de la factura
    private int $_idInvoice;

    private int     $_idAdministrador;
    private string  $_administrador;
    private int     $_idComunidad;
    private string  $_comunidad;
    private string  $_email;
    //  Concepto de la factura rectificativa
    private string  $_concepto;
    //  Número de la factura
    private string  $_numero;
    //  Importe de la factura rectificativa
    private float   $_importe;
    //  Nombre del fichero que se ha generado
    private string  $_nombrefichero;
    //  Total impuestos incluidos
    private float   $_totalTaxesInc;
    //  Total impuestos excluidos
    private float   $_totalTaxesExc;
    //  % IVA
    private float   $_taxRate;
    //  ID usuario creación
    private int     $_usercreate;
    //  Fecha de creación
    private string  $_created;
    //  Fecha de actualización
    private string  $_updated;

    /**
     * ID del detalle
     */
    public function Id(){return $this->_id;}
    public function SetId($value){
        $this->_id = (int)$value; return $this;
    }

    /**
     * ID de la factura
     */
    public function IdInvoice(){return $this->_idInvoice;}
    public function SetIdInvoice($value){
        $this->_idInvoice = (int)$value; 
        return $this;
    }

    /**
     * ID del administrador
     */
    public function IdAdministrador(){ return $this->_idAdministrador;}
    public function SetIdAdministrador($value){
        $this->_idAdministrador = $value;
        return $this;
    }

    /**
     * Nombre del administrador
     */
    public function Administrador(){ return $this->_administrador;}
    public function SetAdministrador($value){
        $this->_administrador = $value;
        return $this;
    }

    /**
     * Id de la comunidad
     */
    public function IdComunidad(){ return $this->_idComunidad;}
    public function SetIdComunidad($value){
        $this->_idComunidad = $value;
        return $this;
    }

    /**
     * Nombre de la comunidad
     */
    public function Comunidad(){ return $this->_comunidad;}
    public function SetComunidad(string $value){
        $this->_comunidad = $value;
        return $this;
    }

    /**
     * E-mail donde se ha enviado la factura
     */
    public function Email(){ return $this->_email; }
    public function SetEmail(string $value){
        $this->_email = $value;
        return $this;
    }


    /**
     * Conceoto
     */
    public function Concepto(){return $this->_concepto;}
    public function SetConcepto(string $value)
    {
        $this->_concepto = $value; 
        return $this;
    }

    /**
     * Nº de factura
     */
    public function Numero(){return $this->_numero;}
    public function SetNumero(string $value){
        $this->_numero = $value; return $this;
    }

    /**
     * Nombre del fichero
     */
    public function NombreFichero(){ return $this->_nombrefichero;}
    public function SetNombreFichero(string|null $value){
        $this->_nombrefichero = $value; return $this;
    }

    /**
     * Total impuestos incluidos
     */
    public function TotalTaxesInc(){return $this->_totalTaxesInc;}
    public function SetTotalTaxesInc(float|null $value)
    {
        $this->_totalTaxesInc = $value; 
        return $this;
    }

    /**
     * Total impuestos excluidos
     */
    public function TotalTaxesExc(){return $this->_totalTaxesExc;}
    public function SetTotalTaxesExc(float|null $value)
    {
        $this->_totalTaxesExc = $value; 
        return $this;
    }

    /**
     * % IVA
     */
    public function TaxRate(){return $this->_taxRate;}
    public function SetTaxRate(float|null $value)
    {
        $this->_taxRate = $value; 
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

        //  Entidad Invoice
        $this->relations[] = $this->addRelation([
            'table' => 'invoice',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idinvoice',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'canReturnSchema' => false,
            'readOnly' => true,
            'deleteMode' => DELETE_FISICO,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);

        //  Entidad Comunidad
        $this->relations[] = $this->addRelation([
            'table' => 'comunidad',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idcomunidad',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'canReturnSchema' => false,
            'readOnly' => true,
            'deleteMode' => DELETE_FISICO,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);

        // Entidad Administrador
        $this->relations[] = $this->addRelation([
            'table' => 'usuario',
            //  Columna de la entidad que se está relacionando con la entidad principal
            'sourceColumn' =>'idadministrador',
            //  Columna de la entidad principal con la que se va a relacionar
            'targetColumn' => 'id',
            'fieldType' => 'int',
            'canReturnSchema' => false,
            'readOnly' => true,
            'deleteMode' => DELETE_FISICO,
            //  Indica si el campo se relaciona desde la entidad relacionada o desde la entidad principal
            'relationType' => RELACION_INVERSA
        ]);
    }

}